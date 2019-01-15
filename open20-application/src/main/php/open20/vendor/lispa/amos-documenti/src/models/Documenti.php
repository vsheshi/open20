<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\models
 * @category   CategoryName
 */

namespace lispa\amos\documenti\models;

use lispa\amos\attachments\behaviors\FileBehavior;
use lispa\amos\attachments\models\File;
use lispa\amos\comments\models\CommentInterface;
use lispa\amos\core\interfaces\ContentModelInterface;
use lispa\amos\core\interfaces\ViewModelInterface;
use lispa\amos\core\views\toolbars\StatsToolbarPanels;
use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\documenti\i18n\grammar\DocumentsGrammar;
use lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard;
use lispa\amos\notificationmanager\behaviors\NotifyBehavior;
use lispa\amos\workflow\behaviors\WorkflowLogFunctionsBehavior;
use pendalf89\filemanager\behaviors\MediafileBehavior;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\log\Logger;

/**
 * Class Documenti
 *
 * This is the model class for table "documenti".
 *
 * @method \cornernote\workflow\manager\components\WorkflowDbSource getWorkflowSource()
 * @method \yii\db\ActiveQuery hasOneFile($attribute = 'file', $sort = 'id')
 * @method \yii\db\ActiveQuery hasMultipleFiles($attribute = 'file', $sort = 'id')
 * @method string|null getRegolaPubblicazione()
 * @method array getTargets()
 *
 * @property \lispa\amos\documenti\models\Documenti[] $allParents
 * @property \lispa\amos\documenti\models\Documenti[] $allDocumentVersions
 * @property string $versionInfo
 *
 * @package lispa\amos\documenti\models
 */
class Documenti extends \lispa\amos\documenti\models\base\Documenti implements ContentModelInterface, CommentInterface, ViewModelInterface
{
    // Workflow ID
    const DOCUMENTI_WORKFLOW = 'DocumentiWorkflow';

    // Workflow states IDS
    const DOCUMENTI_WORKFLOW_STATUS_BOZZA = 'DocumentiWorkflow/BOZZA';
    const DOCUMENTI_WORKFLOW_STATUS_DAVALIDARE = 'DocumentiWorkflow/DAVALIDARE';
    const DOCUMENTI_WORKFLOW_STATUS_VALIDATO = 'DocumentiWorkflow/VALIDATO';
    const DOCUMENTI_WORKFLOW_STATUS_NONVALIDATO = 'DocumentiWorkflow/NONVALIDATO';

    /**
     * Create Document scenario
     */
    const SCENARIO_CREATE = 'document_create';
    const SCENARIO_UPDATE = 'document_update';
    const SCENARIO_FOLDER = 'scenario_folder';

    /**
     * All the scenarios listed below are for the wizard.
     */
    const SCENARIO_INTRODUCTION = 'scenario_introduction';
    const SCENARIO_DETAILS = 'scenario_details';
    const SCENARIO_PUBLICATION = 'scenario_publication';
    const SCENARIO_SUMMARY = 'scenario_summary';

    /** Secenarios for hide pubblication date */
    const SCENARIO_DETAILS_HIDE_PUBBLICATION_DATE = 'scenario_details_hide_pubblication_date';
    const SCENARIO_CREATE_HIDE_PUBBLICATION_DATE = 'scenario_create_hide_pubblication_date';
    const SCENARIO_UPDATE_HIDE_PUBBLICATION_DATE = 'scenario_update_hide_pubblication_date';

    // Is folder constants
    const IS_FOLDER = 1;
    const IS_DOCUMENT = 0;

    /**
     * @var string $regola_pubblicazione Regola di pubblicazione
     */
    public $regola_pubblicazione;

    /**
     * @var string $destinatari Destinatari
     */
    public $destinatari;

    /**
     * @var string $validatori Validatori
     */
    public $validatori;

    /**
     * @var string $distance Distanza
     */
    public $distance;

    /**
     * @var string $destinatari_pubblicazione Destinatari pubblicazione
     */
    public $destinatari_pubblicazione;

    /**
     * @var string $destinatari_notifiche Destinatari notifiche
     */
    public $destinatari_notifiche;

    /**
     * @var mixed $file File
     */
    public $file;

    /**
     * @var File $documentMainFile
     */
    private $documentMainFile;

    /**
     * @var File[] $documentAttachments
     */
    private $documentAttachments;

    private static $categories;

    /**
     * @var AmosDocumenti $documentsModule
     */
    protected $documentsModule = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->documentsModule = Yii::$app->getModule(AmosDocumenti::getModuleName());

        if ($this->isNewRecord) {
            $this->status = $this->getWorkflowSource()->getWorkflow(self::DOCUMENTI_WORKFLOW)->getInitialStatusId();
            if(!is_null($this->documentsModule)){
                if($this->documentsModule->hidePubblicationDate) {
                    // the news will be visible forever
                    $this->data_rimozione =  '9999-12-31';
                }
                $this->data_pubblicazione = date("Y-m-d");
            }
            if ($this->documentsModule && $this->documentsModule->enableDocumentVersioning && !$this->is_folder) {
                $this->version = 1;
            }
            if (($this->scenario == self::SCENARIO_CREATE) || ($this->scenario == self::SCENARIO_DETAILS) || ($this->scenario == self::SCENARIO_CREATE_HIDE_PUBBLICATION_DATE) || ($this->scenario == self::SCENARIO_DETAILS_HIDE_PUBBLICATION_DATE)) {
                $query = new Query();
                if (!self::$categories) {
                    self::$categories = $query->from(DocumentiCategorie::tableName())->all();
                }
                $countCategories = count(self::$categories);
                if ($countCategories == 1) {
                    $this->documenti_categorie_id = self::$categories[0]['id'];
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules =  ArrayHelper::merge(parent::rules(), [
            [['destinatari_pubblicazione', 'destinatari_notifiche'], 'safe'],
            [['documentMainFile'], 'required', 'message' => "E' necessario caricare 1 documento"],
            [['documentAttachments'], 'file', 'maxFiles' => 0],
            [['documentMainFile'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 1, 'on' =>  self::SCENARIO_UPDATE],
            [['documentMainFile'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 1, 'on' => self::SCENARIO_CREATE],
        ]);

        if($this->scenario != self::SCENARIO_DETAILS_HIDE_PUBBLICATION_DATE && $this->scenario != self::SCENARIO_CREATE_HIDE_PUBBLICATION_DATE && $this->scenario != self::SCENARIO_UPDATE_HIDE_PUBBLICATION_DATE) {
            $rules = ArrayHelper::merge($rules, [
                [['data_pubblicazione', 'data_rimozione'], 'required'],
                ['data_pubblicazione', 'compare', 'compareAttribute' => 'data_rimozione', 'operator' => '<='],
                ['data_rimozione', 'compare', 'compareAttribute' => 'data_pubblicazione', 'operator' => '>='],
            ]);
        }
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'documentMainFile' => AmosDocumenti::t('amosdocumenti', '#MAIN_DOCUMENT'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {

        $parentScenarios = parent::scenarios();
        $scenarios = ArrayHelper::merge(
            $parentScenarios,
            [
                self::SCENARIO_CREATE => $parentScenarios[self::SCENARIO_DEFAULT]
            ]
        );
        $scenarios[self::SCENARIO_DETAILS] = [
            'documentMainFile',
            'titolo',
            'inviaNotifiche',
            'sottotitolo',
            'descrizione_breve',
            'descrizione',
            'documenti_categorie_id',
            'data_pubblicazione',
            'data_rimozione',
            'comments_enabled',
            'status'
        ];
        $scenarios[self::SCENARIO_PUBLICATION] = [
            'destinatari_pubblicazione',
            'destinatari_notifiche'
        ];
        $scenarios[self::SCENARIO_SUMMARY] = [
            'status'
        ];
        $scenarios[self::SCENARIO_FOLDER] = [
            'titolo',
            'data_pubblicazione',
            'data_rimozione',
            'status'
        ];

        $scenarios[self::SCENARIO_UPDATE] =  $scenarios[self::SCENARIO_CREATE];

        /** @var AmosDocumenti $documentiModule */
        $documentiModule = Yii::$app->getModule(AmosDocumenti::getModuleName());
        if ($documentiModule->params['site_publish_enabled']) {
            $scenarios[self::SCENARIO_DETAILS][] = 'primo_piano';
        }
        if ($documentiModule->params['site_featured_enabled']) {
            $scenarios[self::SCENARIO_DETAILS][] = 'in_evidenza';
        }

        $scenarios[self::SCENARIO_DETAILS_HIDE_PUBBLICATION_DATE] = $scenarios[self::SCENARIO_DETAILS];
        $scenarios[self::SCENARIO_CREATE_HIDE_PUBBLICATION_DATE] = $scenarios[self::SCENARIO_CREATE];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'mediafile' => [
                'class' => MediafileBehavior::className(),
                'name' => get_class($this),
                'attributes' => [
                    'filemanager_mediafile_id',
                ],
            ],
            'workflow' => [
                'class' => SimpleWorkflowBehavior::className(),
                'defaultWorkflowId' => self::DOCUMENTI_WORKFLOW,
                'propagateErrorsToModel' => true
            ],
            'workflowLog' => [
                'class' => WorkflowLogFunctionsBehavior::className()
            ],
            'NotifyBehavior' => [
                'class' => NotifyBehavior::className(),
                'conditions' => [],
            ],
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'titolo'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getGridViewColumns()
    {
        return [
            'titolo' => [
                'attribute' => 'titolo',
                'headerOptions' => [
                    'id' => 'titolo'
                ],
                'contentOptions' => [
                    'headers' => 'titolo'
                ]
            ],
            'descrizione' => [
                'attribute' => 'descrizione',
                'format' => 'html',
                'headerOptions' => [
                    'id' => 'descrizione'
                ],
                'contentOptions' => [
                    'headers' => 'descrizione'
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getViewUrl()
    {
        return "documenti/documenti/view";
    }

    /**
     * @inheritdoc
     */
    public function getFullViewUrl()
    {
        return Url::toRoute(["/" . $this->getViewUrl(), "id" => $this->id]);
    }

    /**
     * @inheritdoc
     */
    public function getToValidateStatus()
    {
        return self::DOCUMENTI_WORKFLOW_STATUS_DAVALIDARE;
    }

    /**
     * @inheritdoc
     */
    public function getValidatedStatus()
    {
        return self::DOCUMENTI_WORKFLOW_STATUS_VALIDATO;
    }

    /**
     * @inheritdoc
     */
    public function getDraftStatus()
    {
        return self::DOCUMENTI_WORKFLOW_STATUS_BOZZA;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorRole()
    {
        return 'VALIDATORE_DOCUMENTI';
    }

    /**
     * @inheritdoc
     */
    public function getPluginWidgetClassname()
    {
        return WidgetIconDocumentiDashboard::className();
    }

    /**
     * Getter for $this->documentMainFile;
     * @return File
     */
    public function getDocumentMainFile()
    {
        if (empty($this->documentMainFile)) {
            $this->documentMainFile = $this->hasOneFile('documentMainFile')->one();
        }
        return $this->documentMainFile;
    }

    /**
     * @param File $doc
     * @return File
     */
    public function setDocumentMainFile($doc)
    {
        return $this->documentMainFile = $doc;
    }

    /**
     * Getter for $this->documentAttachments;
     * @return File[]
     */
    public function getDocumentAttachments()
    {
        if (empty($this->documentAttachments)) {
            $this->documentAttachments = $this->hasMultipleFiles('documentAttachments')->one();
        }
        return $this->documentAttachments;
    }

    /**
     * @param $attachments
     * @return File
     */
    public function setDocumentAttachments($attachments)
    {
        return $this->documentAttachments = $attachments;
    }

    /**
     * @inheritdoc
     */
    public function isCommentable()
    {
        return $this->comments_enabled;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->titolo;
    }

    /**
     * @inheritdoc
     */
    public function getDescription($truncate)
    {
        $ret = $this->descrizione;
        if ($truncate) {
            $ret = $this->__shortText($this->descrizione, 200);
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function getStatsToolbar()
    {
        $panels = [];
        $count_comments = 0;

        try {
            $panels = parent::getStatsToolbar();
            $filescount = $this->getFileCount() - 1;
            $panels = ArrayHelper::merge($panels, StatsToolbarPanels::getDocumentsPanel($this, $filescount));
            if ($this->isCommentable()) {
                $commentModule = \Yii::$app->getModule('comments');
                if ($commentModule) {
                    /** @var \lispa\amos\comments\AmosComments $commentModule */
                    $count_comments = $commentModule->countComments($this);
                }
                $panels = ArrayHelper::merge($panels, StatsToolbarPanels::getCommentsPanel($this, $count_comments));
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $panels;
    }

    /**
     * @inheritdoc
     */
    public function getPublicatedFrom()
    {
        return $this->data_pubblicazione;
    }

    /**
     * @inheritdoc
     */
    public function getPublicatedAt()
    {
        return $this->data_rimozione;
    }

    /**
     * @inheritdoc
     */
    public function getCategory()
    {
        return $this->hasOne(\lispa\amos\documenti\models\DocumentiCategorie::className(), ['id' => 'documenti_categorie_id']);
    }

    /**
     * @return DocumentsGrammar|mixed
     */
    public function getGrammar()
    {
        return new DocumentsGrammar();
    }

    /**
     * @return array list of statuses that for cwh is validated
     */
    public function getCwhValidationStatuses()
    {
        return [$this->getValidatedStatus()];
    }

    /**
     * @return array
     */
    public function getAllParents()
    {
        $currentModel = $this;
        $parentsList = [];
        while (!is_null($currentModel->parent)) {
            $parentsList = array_merge(
                [$currentModel->parent],
                $parentsList
            );
            $currentModel = $currentModel->parent;
        }
        return $parentsList;
    }

    /**
     * Search all children recursively
     * @param array $children
     * @return array
     */
    public function getAllChildrens($children = [])
    {
        $currentModel = $this;
        $childrenList = $children;

        if(count($currentModel->children)==0){
            return [];
        }

        /** @var  $documento  Documenti*/
        foreach ($currentModel->children as $documento) {
            $childrenList[]= $documento->id;
            $childrenList = ArrayHelper::merge($childrenList, $documento->getAllChildrens());
        }

        $childrenList []= $this->id;
        return $childrenList;
    }

    /**
     * Search all document children recursively
     * @param array $children
     * @return array
     */
    public function getAllDocumentChildrens()
    {
        $arrayChildren = [];
        $children = $this->getAllChildrens();
        foreach ($children as $childId) {
            $child = Documenti::findOne($childId);
            if(!$child->is_folder && $child->version_parent_id == null) {
                $arrayChildren[] = $child->id;
            }
        }
        return array_values($arrayChildren);

    }

    /**
     * Search all document in the first level
     * @param array $children
     * @return array
     */
    public function getDocumentChildrens()
    {
        $arrayChildren = [];
        $children = $this->children;
        foreach ($children  as $child){
            if(!$child->is_folder && $child->version_parent_id == null) {
                $arrayChildren [] = $child->id;
            }
        }

        return $arrayChildren;
    }

    /**
     * @return Documenti[]
     */
    public function getAllDocumentVersions()
    {
        /** @var ActiveQuery $query */
        $query = Documenti::find();
        if (is_null($this->version_parent_id)) {
            $query->andWhere(['or',
                ['version_parent_id' => $this->id],
                ['id' => $this->id]
            ]);
        } else {
            $query->andWhere(['or',
                ['version_parent_id' => $this->version_parent_id],
                ['id' => $this->version_parent_id]
            ]);
        }
        $query->orderBy(['version' => SORT_ASC]);
        $allModels = $query->all();
        return $allModels;
    }

    /**
     * @return Documenti
     */
    public function getLastOldDocumentVersion()
    {
        $query = new Query();
        $query->from(self::tableName());
        $query->andWhere(['version_parent_id' => $this->id, 'deleted_at' => null]);
        $maxVersion = $query->max('version');
        $document = Documenti::find()->andWhere([
            'version_parent_id' => $this->id,
            'version' => $maxVersion
        ])->one();
        return $document;
    }

    /**
     * @return bool
     */
    public function makeNewDocumentVersion()
    {
        // If the document versioning is disabled do the standard operations.
        if (!$this->documentsModule->enableDocumentVersioning || $this->is_folder || ($this->version == -1)) {
            return true;
        }
        $newDocument = new Documenti();
        $newDocument->setAttributes($this->attributes);
        $newDocument->version_parent_id = $this->id;
        $newDocument->version = $this->version;
        $newDocument->detachBehavior('cwhBehavior');
        $ok = $newDocument->save(false);
        if ($ok) {
            $ok = $this->duplicateDocumentMainFile($newDocument);
        }
        if ($ok) {
            $ok = $this->duplicateDocumentAttachments($newDocument);
        }
        if ($ok) {
            $this->version = $this->getNextVersion();
            $ok = $this->save(false);
        }
        return $ok;
    }

    /**
     * @return bool
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteNewDocumentVersion()
    {
        try {
            $lastOldDocument = $this->getLastOldDocumentVersion();
            if (is_null($lastOldDocument)) {
                return false;
            }
            // if you click su delete file before you click on "cancel version", you don't need to cancel the file because is already deleted
            $this->deleteThisDocumentMainFileRow();
            $ok = $lastOldDocument->duplicateDocumentMainFile($this);
            if ($ok) {
                $ok = $lastOldDocument->duplicateDocumentAttachments($this);
            }
            if ($ok) {
                $this->version = $lastOldDocument->version;
                $ok = $this->save(false);
            }
            if ($ok) {
                $lastOldDocument->delete();
                $ok = !$lastOldDocument->hasErrors();
            }
        } catch ( Exception $e){
            return false;
        }
        return $ok;
    }

    /**
     * @return false|int
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteThisDocumentMainFileRow()
    {
        $file = File::findOne([
            'model' => Documenti::className(),
            'attribute' => 'documentMainFile',
            'itemId' => $this->id
        ]);
        //if file
        $ok = false;
        if($file) {
            $ok = $file->delete();
        }
        return $ok;
    }

    /**
     * @return false|int
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function deleteThisDocumentAttachmentRows()
    {
        $files = File::find()->andWhere([
            'model' => Documenti::className(),
            'attribute' => 'documentMainFile',
            'itemId' => $this->id
        ])->all();
        if (count($files) == 0) {
            return true;
        }
        $allOk = true;
        foreach ($files as $file) {
            /** @var File $file */
            $ok = $file->delete();
            if (!$ok) {
                $allOk = false;
            }
        }
        return $allOk;
    }

    /**
     * @param Documenti $newDocument
     * @return bool
     */
    public function duplicateDocumentMainFile($newDocument)
    {
        $oldFile = File::findOne([
            'model' => Documenti::className(),
            'attribute' => 'documentMainFile',
            'itemId' => $this->id
        ]);
        if (is_null($oldFile)) {
            return true;
        }
        $ok = $this->duplicateOldFile($oldFile, $newDocument->id);
        return $ok;
    }

    /**
     * @param Documenti $newDocument
     * @return bool
     */
    public function duplicateDocumentAttachments($newDocument)
    {
        $oldFiles = File::find()->andWhere([
            'model' => Documenti::className(),
            'attribute' => 'documentAttachments',
            'itemId' => $this->id
        ])->all();
        if (count($oldFiles) == 0) {
            return true;
        }
        $allOk = true;
        foreach ($oldFiles as $oldFile) {
            /** @var File $oldFile */
            $ok = $this->duplicateOldFile($oldFile, $newDocument->id);
            if (!$ok) {
                $allOk = false;
            }
        }
        return $allOk;
    }

    /**
     * @param File $oldFile
     * @param int $newDocumentId
     * @return bool
     */
    private function duplicateOldFile($oldFile, $newDocumentId)
    {
        $file = new File();
        $file->setAttributes($oldFile->attributes);
        $file->itemId = $newDocumentId;
        $ok = $file->save(false);
        return $ok;
    }

    /**
     * @return int
     */
    public function getNextVersion()
    {
        $query = new Query();
        $query->from(self::tableName());
        $max = $this->version;
        if (!is_null($this->version_parent_id)) {
            $query->andWhere(['version_parent_id' => $this->version_parent_id, 'deleted_at' => null]);
            $max = $query->max('version');
        }
        return (!$max ? 1 : ($max + 1));
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getVersionInfo()
    {
        return $this->getAttributeLabel('version') . ' ' . $this->version . ' - ' . Yii::$app->formatter->asDatetime($this->updated_at);
    }

    /**
     *
     */
    public function setDetailScenario(){
        $moduleNews = \Yii::$app->getModule(AmosDocumenti::getModuleName());
        if($moduleNews->hidePubblicationDate == true){
            $this->setScenario(Documenti::SCENARIO_DETAILS_HIDE_PUBBLICATION_DATE);
        }
        else {
            $this->setScenario(Documenti::SCENARIO_DETAILS);
        }
    }

    public function getNotifichePreferenzeProfili()
    {
        return ArrayHelper::map(DocumentiNotifichePreferenze::find()
            ->andWhere(['documento_parent_id' => empty($this->version_parent_id) ? $this->id : $this->version_parent_id])
            ->andWhere(['not', ['user_id' => null]])
            ->all(), 'id', 'user_id');
    }

    public function getNotifichePreferenzeGruppi()
    {
        return ArrayHelper::map(DocumentiNotifichePreferenze::find()
            ->andWhere(['documento_parent_id' => empty($this->version_parent_id) ? $this->id : $this->version_parent_id])
            ->andWhere(['not', ['group_id' => null]])
            ->all(), 'id', 'group_id');
    }

}
