<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    pcd20-import
 * @category   CategoryName
 */

namespace pcd20\import\controllers;

use lispa\amos\attachments\FileModule;
use lispa\amos\community\AmosCommunity;
use lispa\amos\community\exceptions\CommunityException;
use lispa\amos\community\models\base\CommunityType;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\cwh\models\CwhConfigContents;
use lispa\amos\cwh\models\CwhPubblicazioni;
use lispa\amos\cwh\models\CwhPubblicazioniCwhNodiEditoriMm;
use lispa\amos\cwh\models\CwhPubblicazioniCwhNodiValidatoriMm;
use lispa\amos\cwh\query\CwhActiveQuery;
use lispa\amos\documenti\models\Documenti;
use pcd20\import\models\ReportNode;
use pcd20\import\models\UploaderImportList;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use pcd20\import\Module;
use yii\helpers\ArrayHelper;

class DefaultController extends Controller
{
    //A tree for each file because is maybe more than one
    protected $dirTrees = [];
    //Root of the project installation
    protected $projectRoothPath;
    //Where the files has to be stocked
    protected $uploadDir;
    //The uploader dir
    protected $uploaderDir;
    //CwhConfig for documents
    /**
     * @var $pubblicationConfig
     */
    protected $pubblicationConfig;

    /**
     * The working community module
     * @var $communityModule AmosCommunity
     */
    protected $communityModule;
    //User selected nodes
    protected $selectedNodes = [];

    /**
     * @var $attachmentsModule FileModule
     */
    protected $attachmentsModule;

    /** @var  $lastDir , can be a community */
//    public $lastDir;
    public $treeForReport = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        //Setup variable used in this class
        $this->initVars();
    }

    /**
     * Set base vars for dir and or more
     */
    protected function initVars()
    {
        //Path alias
        $pathAlias = \Yii::getAlias('@app/../');

        //Root of the project installation
        $this->projectRoothPath = realpath($pathAlias);

        //Where the files has to be stocked
        $this->uploadDir = $this->projectRoothPath.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'uploads';

        //The uploader dir
        $this->uploaderDir = $this->uploadDir.DIRECTORY_SEPARATOR.'uploader';
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * This action is used to extrack all uploaded files
     * @throws Exception
     */
    public function actionExtract($communityId = null)
    {
        //Uploaded file data
        $getData = $this->getData();

        foreach ($getData as $file) {
            //The file location
            $uploadFilePath = $this->projectRoothPath.DIRECTORY_SEPARATOR.$file['path'];

            //Dir location of extracted files
            $dirLocation = $this->uploaderDir.DIRECTORY_SEPARATOR.'extract_'.$file['hash'];

            //Extract the zip and return true if succeed
            $result = $this->extractZip($uploadFilePath, $dirLocation);

            // If the file is successfully extracted, redirect to the view, otherwise an error will be displayed
            if ($result == '0') {
                return $this->redirect([
                        'choose-nodes',
                        'item' => $file['hash'],
                        'communityId' => $communityId
                ]);
            } else {
                throw new Exception(Module::t('pcd20import', 'Unable to Unzip File').' '.$uploadFilePath);
            }
        }
    }

    /**
     * @param $item
     * @return string
     */
    public function actionChooseNodes($item, $communityId = null)
    {
        //Dir location of extracted files
        $dirLocation = $this->uploaderDir.DIRECTORY_SEPARATOR.'extract_'.$item;

        //Array Tree of the directory
        $dirTree = $this->dirTree($dirLocation,false,true);

        return $this->render('choose-nodes',
                [
                'dirTree' => $dirTree,
                'item' => $item,
                'communityId' => $communityId
        ]);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function actionBuildPlatform()
    {
        set_time_limit(0);

        //Selected nodes
        $this->selectedNodes = json_decode(\Yii::$app->request->post('nodes'));

        //Selected name (not required
        $selectedName = \Yii::$app->request->post('name');

        $communityCreated = null;

        //Community ID for override
        $communityId = \Yii::$app->request->post('communityId');

        //Extracted item
        $item = \Yii::$app->request->post('item');

        //Check correct data
        if (empty($item)) {
            throw new Exception(Module::t('pcd20import', 'Wrong Data, Try Later'));
            return false;
        }

        //Dir location of extracted files
        $dirLocation = $this->uploaderDir.DIRECTORY_SEPARATOR.'extract_'.$item;

        //Array Tree of the directory
        $dirTree = $this->dirTree($dirLocation, true);

        //Env vars setup used by document generation
        $this->setupEnv();

        //Push root node if not set
        if (!in_array($dirTree['dataAttr']['path'], $this->selectedNodes)) {
            $this->selectedNodes = ArrayHelper::merge([$dirTree['dataAttr']['path']], $this->selectedNodes);
        }

        if ($communityId) {
            $communitySearch = Community::findOne(['id' => $communityId]);
            $selectedName    = $communitySearch->name;

            $communityCreated = $communitySearch->id;
        }

        $importation            = new UploaderImportList();
        $importation->name_file = $selectedName;
        $importation->path_log  = $importation->getPathForLog();
        $importation->save();

//pr($importation, 'test');
        //If the community is set i must override current one
        if ($communityId) {
            //Generate all docs in this tree node
            $this->treeForReport [] = new ReportNode([
                'type' => ReportNode::COMMUNITY,
                'id' => $communityCreated,
                'name' => $selectedName ? $selectedName : 'default',
                'logfile' => $importation->path_log
            ]);

            $documentsTree = $this->createDocumentsInTree($dirTree, $communityCreated, null, true);
        } else {
            //Start community generation
            $communityCreated = $this->createCommunityByNode($dirTree, null, $selectedName, $importation->path_log);
        }

        if (empty($communityCreated)) {
            throw new Exception(Module::t('pcd20import', 'The community does not exists'));
            return false;
        }

        $cwhModule = \Yii::$app->getModule('cwh');
        if (!is_null($cwhModule)) {
            $cwhModule->resetCwhMaterializatedView();
        }

        $importation->successfull = true;
        $importation->save();

        return $this->render('report',
                [
                'importation' => $importation,
                'communityCreated' => $communityCreated
        ]);
        //Go to your main community
//        return $this->redirect(['/community/join/index' , 'id' => $communityCreated]);
    }

    /**
     * @return \yii\web\Response
     * @throws CommunityException
     * @throws Exception
     */
    public function actionImport()
    {
        set_time_limit(0);

        $communityId = \Yii::$app->request->get('communityId');

        if (!$communityId) {
            throw new Exception('Need communityid');
        }

        //Uploaded file data
        $getData = $this->getData();

        foreach ($getData as $file) {
            //The file location
            $uploadFilePath = $this->projectRoothPath.DIRECTORY_SEPARATOR.$file['path'];

            //Dir location of extracted files
            $dirLocation = $this->uploaderDir.DIRECTORY_SEPARATOR.'extract_'.$file['hash'];

            //Extract the zip and return true if succeed
            $result = $this->extractZip($uploadFilePath, $dirLocation);

            // If the file is successfully extracted, redirect to the view, otherwise an error will be displayed
            if ($result == '0') {
                return $this->redirect([
                        'choose-nodes',
                        'item' => $file['hash'],
                        'communityId' => $communityId
                ]);
            } else {
                throw new Exception(Module::t('pcd20import', 'Unable to Unzip File').' '.$uploadFilePath);
            }
        }
    }

    /**
     * Setup env vars
     */
    public function setupEnv()
    {
        //Trovo config per pubblicazione contenuto
        $this->pubblicationConfig = CwhConfigContents::findOne(['tablename' => Documenti::tableName()]);

        //require community module
        $this->communityModule = \Yii::$app->getModule('community');

        //Setup attachments module
        $this->attachmentsModule = \Yii::$app->getModule('attachments');
    }

    /**
     * @return array|bool|mixed
     * @throws Exception
     */
    public function getData()
    {
        $getData = \Yii::$app->request->get();

        //Passed GET data wich contains files
        $getData = $getData['files'];

        //Check if files is passed correctly
        if (!is_array($getData)) {
            throw new Exception(Module::t('pcd20import', 'Invalid data, try later'));
            return false;
        }

        return $getData;
    }

    /**
     * @param $uploadFilePath
     * @param $dirLocation
     * @return string
     */
    public function extractZip($uploadFilePath, $dirLocation)
    {
        set_time_limit(0);

        // Base 7zip command for extracting a compressed file
        $unzipBaseCommand = "7za x ";

        // Building the full command with the upload file path and the location directory
        // where the compressed file will be extracted
        $unzipCommand = $unzipBaseCommand.$uploadFilePath.' -o'.$dirLocation;

        // Executing the unzip command build above
        exec($unzipCommand);

        // Catching the result of the command run above
        return exec('echo $?');
    }

    /**
     * @param array $node The Tree node for the community
     * @param int $parentCommunity The parent community
     * @param string $communityName Override the name of the community
     * @return mixed
     * @throws Exception|CommunityException
     */
    protected function createCommunityByNode($node, $parentCommunity = null, $communityName = null, $logfile = null)
    {
        //Create the new community
        try {
            $communityId = $this->createCommunity($node, $parentCommunity, $communityName);
            if (!$communityId) {
                throw new Exception(Module::t('pcd20', 'Community not created ').$node);
                return false;
            }
            $this->treeForReport [] = new ReportNode([
                'type' => ReportNode::COMMUNITY,
                'id' => $communityId,
                'name' => $communityName ? $communityName : $node['text'],
                'logfile' => $logfile
            ]);
        } catch (Exception $e) {
            $this->treeForReport [] = new ReportNode([
                'type' => ReportNode::COMMUNITY,
                'id' => $communityId,
                'name' => $communityName ? $communityName : $node['text'],
                'logfile' => $logfile
            ]);

            return null;
        }

        //Generate all docs in this tree node
        $documentsTree = $this->createDocumentsInTree($node, $communityId, null, true);

        return $communityId;
    }

    /**
     * @param array $node
     * @param int $parentCommunity
     * @param string $communityName Override the community name
     * @return int
     * @throws CommunityException
     */
    protected function createCommunity($node, $parentCommunity = null, $communityName = null)
    {
        //Set community name or override with user selected name
        $name = empty($communityName) ? $node['text'] : $communityName;

        //Create first community
        $communityId = $this->communityModule->createCommunity(
            $name, \lispa\amos\community\models\CommunityType::COMMUNITY_TYPE_CLOSED, Community::className(),
            CommunityUserMm::ROLE_COMMUNITY_MANAGER, '', null, CommunityUserMm::STATUS_ACTIVE, \Yii::$app->user->id
        );

        //if creation goes wrong the id is a FALSE
        if (!$communityId) {
            throw new Exception(Module::t('pcd20import', 'Unable to create the community'));
        }

        //the new community record
        $communityRecord = Community::findOne(['id' => $communityId]);

        //Set Community Validated
        $communityRecord->status         = Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED;
        $communityRecord->validated_once = true;

        //Set Parent community
        if ($parentCommunity) {
            //Set parent and save
            $communityRecord->parent_id = $parentCommunity;
        }

        $communityRecord->save(false);

        return $communityId;
    }

    /**
     * @param array $node The Tree node for the community
     * @param int $communityId the owner community
     * @param Documenti|null $parentDoc The parent document dir
     * @param boolean $skipRoot the root node has to be skipped?
     * @param boolean $override Override existing communities if in update mode
     * @throws Exception|CommunityException
     */
    protected function createDocumentsInTree($node, $communityId, $parentDoc = null, $skipRoot = false,
                                             $override = false)
    {
        //Directory config data
        $directoryData = [
            'name' => $node['text'],
            'path' => $node['dataAttr']['path']
        ];

        //The first level for each community has to be skipped
        if (!$skipRoot) {
            //Create node directory
            $nodeDirectory = $this->createDocument($directoryData, $parentDoc, true, $communityId);
            //Avoid to create files under the directory that failed to be created
            if (is_null($nodeDirectory)) {
                return false;
            }
            //Attach cwh rules to this document
        } else {
            $nodeDirectory = null;
        }

        //Create all attached documents
        foreach ($node['files'] as $file) {
            //Directory config data
            $documentData = [
                'name' => $file['name'],
                'path' => $file['path']
            ];

            //Create the document
            $documento = $this->createDocument($documentData, $nodeDirectory, false, $communityId);
        }

        //Create sub nodes
        foreach ($node['nodes'] as $subNode) {
            if (in_array($subNode['dataAttr']['path'], $this->selectedNodes)) {
                //Check if the community exists (only in override mode)
                $communityExists = false;

                if ($override) {
                    //the new community record
                    $communityRecord = Community::findOne(['name' => $subNode['text'], 'parent_id' => $communityId]);

                    //Set parent and save
                    $communityExists = ($communityRecord && $communityRecord->id);

                    if ($communityExists) {
                        //Create only a new tree in documents
                        $this->createDocumentsInTree($subNode, $communityRecord->id, $nodeDirectory, false, $override);
                    }
                }

                if (!$communityExists) {
                    //Create a new community for this node
                    $this->createCommunityByNode($subNode, $communityId);
                }

                //Remove this node from the array
                ReportNode::popDirectoryPath($subNode['text']);
            } else {
                //Create only a new tree in documents
                $this->createDocumentsInTree($subNode, $communityId, $nodeDirectory, false, $override);
            }
        }

        if (!$skipRoot) {
            ReportNode::popDirectoryPath($node['text']);
        }

        //Return this node
        return $nodeDirectory;
    }

    /**
     * @param array $documentData the data for this file as and array
     * @param Community $community
     * @param Documenti $parentDoc The parent document
     * @param bool $isDir is a directory or file document
     * @return Documenti
     */
    public function createDocument($documentData, $parentDoc = null, $isDir = false, $communityId)
    {
        //Create the new document
        try {
            $documento             = new Documenti();
            $documento->titolo     = $documentData['name'];
            $documento->created_by = \Yii::$app->getUser()->getId();
            $documento->created_at = date('Y-m-d H:i:s');

            //If no parent is set
            if (!is_null($parentDoc)) {
                $documento->parent_id = $parentDoc->id;
            }

            $documento->detachBehaviors();
            $documento->status = Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO;

            $documento->is_folder = $isDir;
            $saved                = $documento->save(false);
            $this->addCwhRules($documento, $communityId);

            //Attach file to module
            if (!$isDir && $saved) {
                $attached = $this->attachmentsModule->attachFile(urldecode($documentData['path']), $documento,
                    'documentMainFile');

                //Check if the file is attached
                if (!$attached) {
                    throw new Exception(Module::t('pcd20', 'Unable to attach file'));
                }
            }

            $this->treeForReport [] = new ReportNode([
                'type' => $isDir ? ReportNode::DIRECTORY : ReportNode::FILE,
                'id' => !empty($documento->id) ? $documento->id : null,
                'name' => $documentData['name']
            ]);
        } catch (\Exception $e) {
            $this->treeForReport [] = new ReportNode([
                'type' => $isDir ? ReportNode::DIRECTORY : ReportNode::FILE,
                'id' => null,
                'name' => $documentData['name']
            ]);
            return $e->getMessage();
        }


        //The created document
        return $documento;
    }

    /**
     * @param Documenti $documento
     * @param int $communityId
     * @return CwhPubblicazioniCwhNodiEditoriMm
     */
    public function addCwhRules($documento, $communityId)
    {
        //Cwh pubblication row
        $cwhPubblicazione                              = new CwhPubblicazioni();
        $cwhPubblicazione->cwh_regole_pubblicazione_id = CwhActiveQuery::RULE_NETWORK;
        $cwhPubblicazione->cwh_config_contents_id      = $this->pubblicationConfig->id;
        $cwhPubblicazione->content_id                  = $documento->id;
        $cwhPubblicazione->detachBehaviors();
        $cwhPubblicazione->save(false);

        //Connect cwh config with community and document
        $editorCwh                       = new CwhPubblicazioniCwhNodiEditoriMm();
        $editorCwh->cwh_config_id        = 3;
        $editorCwh->cwh_network_id       = $communityId;
        $editorCwh->cwh_pubblicazioni_id = $cwhPubblicazione->id;
        $editorCwh->cwh_nodi_id          = "community-".$communityId;
        $editorCwh->detachBehaviors();
        $editorCwh->save(false);

        //Cwh Validators node
        $validatorCwh                       = new CwhPubblicazioniCwhNodiValidatoriMm();
        $validatorCwh->cwh_config_id        = 3;
        $validatorCwh->cwh_network_id       = $communityId;
        $validatorCwh->cwh_pubblicazioni_id = $cwhPubblicazione->id;
        $validatorCwh->cwh_nodi_id          = "community-".$communityId;
        $validatorCwh->detachBehaviors();
        $validatorCwh->save(false);

        //Return the mm row
        return $editorCwh;
    }

    /**
     * @param $path
     * @param $withFiles Put files into array?
     * @return array
     */
    protected function dirTree($path, $withFiles = false, $skipRoot = false)
    {
        //Scan dir and return tree
        $dirContent = scandir($path);

        //Sort result
        natcasesort($dirContent);

        //Current Dir Base name
        $baseName = basename($path);

        //List of directories
        $dirs = [
            'text' => utf8_encode($baseName),
            'hideCheckbox' => $skipRoot,
            'dataAttr' => [
                'path' => urlencode($path)
            ],
            'nodes' => [],
            'files' => []
        ];

        //Parse dirs
        foreach ($dirContent as $item) {
            if (!in_array($item, ['.', '..'])) {
                if (is_dir($path.'/'.$item)) {
                    $dirs['nodes'][] = $this->dirTree($path.'/'.($item), $withFiles);
                } elseif ($withFiles) {
                    $dirs['files'][] = [
                        'name' => utf8_encode($item),
                        'path' => urlencode($path.'/'.$item)
                    ];
                }
            }
        }

        return $dirs;
    }

    /**
     * @return string
     */
    public function actionImportList()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UploaderImportList::find()->orderBy('created_at DESC')
        ]);

        return $this->render('import_list', ['dataProvider' => $dataProvider]);
    }

    /**
     * @param $filename
     */
    public function actionGenerateExcel($id)
    {
        $import = UploaderImportList::findOne($id);
        if ($import) {
            ReportNode::generateExcellFromFile($import->path_log);
        }
    }
}