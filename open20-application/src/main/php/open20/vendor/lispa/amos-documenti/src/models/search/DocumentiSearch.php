<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti
 * @category   CategoryName
 */

namespace lispa\amos\documenti\models\search;

use lispa\amos\core\module\AmosModule;
use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\documenti\models\Documenti;
use lispa\amos\notificationmanager\base\NotifyWidget;
use lispa\amos\notificationmanager\models\NotificationChannels;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\di\Container;

/**
 * DocumentiSearch represents the model behind the search form about `lispa\amos\documenti\models\Documenti`.
 */
class DocumentiSearch extends Documenti
{
    private $container;

    public $parentId;

    public function __construct(array $config = [])
    {
        $this->container = new Container();
        $this->container->set('notify',Yii::$app->getModule('notify'));
        parent::__construct($config);
    }

    /**
     */
    public function rules()
    {
        return [
            [['id', 'primo_piano', 'hits', 'abilita_pubblicazione', 'documenti_categorie_id', 'created_by', 'updated_by', 'deleted_by', 'parent_id'], 'integer'],
            [['parentId', 'titolo', 'sottotitolo', 'descrizione_breve', 'descrizione', 'metakey', 'metadesc', 'data_pubblicazione', 'data_rimozione', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    /**
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();

        $behaviors = [];
        //if the parent model News is a model enabled for tags, NewsSearch will have TaggableBehavior too
        $moduleTag = \Yii::$app->getModule('tag');
        if (isset($moduleTag) && in_array(Documenti::className(), $moduleTag->modelsEnabled) && $moduleTag->behaviors) {
            $behaviors = ArrayHelper::merge($moduleTag->behaviors, $behaviors);
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * Documents search method
     *
     * @param array $params
     * @param string $queryType
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function search($params, $queryType, $limit = null)
    {
        $query = $this->buildQuery($queryType, $params);
        $query->limit($limit);

        /** @var  $notify AmosNotify*/
        $notify = $this->getNotifier();
        if($notify)
        {
            $notify->notificationOff(Yii::$app->getUser()->id, Documenti::className(),$query,NotificationChannels::CHANNEL_READ);
        }
        $dp_params = ['query' => $query,];
        if($limit){
            $dp_params ['pagination'] = false;
        }
        //set the data provider
        $dataProvider = new ActiveDataProvider($dp_params);
        //check if can use the custom module order
        if ($this->canUseModuleOrder()) {
            $dataProvider->setSort($this->createOrderClause());
        } else { //for widget graphic last news, order is incorrect without this else
            $dataProvider->setSort([
                'defaultOrder' => [
                    'data_pubblicazione' => SORT_DESC
                ]
            ]);
        }
        //overwrite default order in case  foldering is enabled
        if(AmosDocumenti::instance()->enableFolders){
            $dataProvider->setSort([
                'defaultOrder' => [
                    'is_folder' => SORT_DESC,
                ]
            ]);
        }

        //if you don't use the seach form, the recursive search is not active
        if (!($this->load($params) && $this->validate())) {
            $query->andWhere(['parent_id' => $this->parentId]);
            return $dataProvider;
        }

        // recursive search
        if(!empty($this->parentId)) {
            $currentFolder = Documenti::findOne($this->parentId);
            $listChildrenId = $currentFolder->getAllChildrens();
//            pr($listChildrenId);
            $query->andWhere(['parent_id' => $listChildrenId]);
        }
        //if parentid empty the search is without (parent_id IS NULL)


        if (isset($params[$this->formName()]['tagValues'])) {

            $tagValues = $params[$this->formName()]['tagValues'];
            $this->setTagValues($tagValues);
            if (is_array($tagValues) && !empty($tagValues)) {
                $andWhere = "";
                $i = 0;
                foreach ($tagValues as $rootId => $tagId) {
                    if (!empty($tagId)) {
                        if ($i == 0) {
                            $query->innerJoin('entitys_tags_mm entities_tag',
                                "entities_tag.classname = '" . addslashes(Documenti::className()) . "' AND entities_tag.record_id=documenti.id");

                        }else{
                            $andWhere .= " OR ";
                        }
                        $andWhere .= "(entities_tag.tag_id in (" . $tagId . ") AND entities_tag.root_id = " . $rootId . " AND entities_tag.deleted_at is null)";
                        $i++;
                    }
                }
                $andWhere .= "";
                if(!empty($andWhere)) {
                    $query->andWhere($andWhere);
                }
            }
        }

        $query->andFilterWhere([
            'documenti.id' => $this->id,
            'documenti.primo_piano' => $this->primo_piano,
            'documenti.hits' => $this->hits,
            'documenti.abilita_pubblicazione' => $this->abilita_pubblicazione,
            'documenti.documenti_categorie_id' => $this->documenti_categorie_id,
            'documenti.created_at' => $this->created_at,
            'documenti.updated_at' => $this->updated_at,
            'documenti.deleted_at' => $this->deleted_at,
            'documenti.created_by' => $this->created_by,
            'documenti.updated_by' => $this->updated_by,
            'documenti.deleted_by' => $this->deleted_by,
//            'parent_id' => $this->parent_id
        ]);

        $documentModel = \Yii::$app->getModule(AmosDocumenti::getModuleName());
        if(isset($documentModel)) {
            if($documentModel->hidePubblicationDate == false) {
                $query->andFilterWhere([
                    'data_pubblicazione' => $this->data_pubblicazione,
                    'data_rimozione' => $this->data_rimozione,
                ]);
            }
        }

        $query->andFilterWhere(['like', 'documenti.titolo', $this->titolo])
            ->andFilterWhere(['like', 'documenti.sottotitolo', $this->sottotitolo])
            ->andFilterWhere(['like', 'documenti.descrizione_breve', $this->descrizione_breve])
            ->andFilterWhere(['like', 'documenti.descrizione', $this->descrizione])
            ->andFilterWhere(['like', 'documenti.metakey', $this->metakey])
            ->andFilterWhere(['like', 'documenti.metadesc', $this->metadesc]);

        return $dataProvider;
    }

    /**
     * Documents base search: all documents matching search parameters and not deleted.
     *
     * @param   array $params Search parameters
     * @return \yii\db\ActiveQuery
     */
    public function baseSearch($params)
    {
        //init the default search values
        $this->initOrderVars();

        //check params to get orders value
        $this->setOrderVars($params);


        /** @var \yii\db\ActiveQuery $baseQuery */
        $baseQuery = Documenti::find()->distinct();
        if ($this->documentsModule->enableDocumentVersioning) {
            $baseQuery->andWhere(['version_parent_id' => null]);
        }

        return $baseQuery;

    }

    /**
     * Search the Documents created by the logged user
     *
     * @param array $params Array di parametri per la ricerca
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function searchOwnDocuments($params, $limit = null)
    {
        return $this->search($params, 'created-by', $limit);
    }

    /**
     * Ritorna solamente $this.
     *
     * @return $this
     */
    public function validazioneAbilitata()
    {
        return $this;
    }

    /**
     * Search documents to validate based on cwh rules if cwh is active, all documents in 'to validate status' otherwise
     *
     * @param array $params Array di parametri per la ricerca
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function searchToValidateDocuments($params, $limit = null)
    {
        return $this->search($params, 'to-validate', $limit);
    }

    /**
     * Search last documents in validated status, generally the limit is set to 3 (by last documents graphic widget)
     *
     * @param array $params Array of search parameters
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function lastDocuments($params, $limit = null)
    {
        $dataProvider = $this->searchAll(Yii::$app->request->getQueryParams(), $limit);

        return $dataProvider;
    }

    /**
     * Search all validated documents
     *
     * @param array $params Array of get parameters for search
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function searchAll($params, $limit = null)
    {
        return $this->search($params, 'all', $limit);
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchAdminAll($params, $limit = null)
    {
        return $this->search($params, 'admin-all', $limit);
    }

    /**
     * Search method useful to retrieve all validated documenti (based on publication rule and visibility).
     *
     * @param array $params Array of get parameters for search
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function searchOwnInterest($params, $limit = null)
    {
        return $this->search($params, 'own-interest', $limit);
    }

    /**
     * Search method useful to retrieve documents in validated status with both flags primo_piano and in_evidenza true
     *
     * @param array $params Array di parametri
     * @return ActiveDataProvider
     */
    public function searchHighlightedAndHomepageDocumenti($params)
    {
        $query = $this->highlightedAndHomepageDocumentiQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'data_pubblicazione' => SORT_DESC,
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    /**
     * Search method useful to retrieve documents in validated status with flag primo_piano = true
     *
     * @param array $params Array di parametri
     * @return ActiveDataProvider
     */
    public function searchHomepageDocuments($params)
    {
        $query = $this->homepageDocumentsQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'data_pubblicazione' => SORT_DESC,
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return \yii\db\ActiveQuery
     */
    public function highlightedAndHomepageDocumentiQuery($params)
    {
        $tableName = $this->tableName();
        $query = $this->baseSearch($params)
            ->where([])
            ->andWhere([
                $tableName . '.status' => Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO,
            ])
            ->andWhere($tableName . '.deleted_at IS NULL')
            ->andWhere($tableName . '.in_evidenza = 1')
            ->andWhere($tableName . '.primo_piano = 1');
        return $query;
    }

    /**
     * @param array $params
     * @return \yii\db\ActiveQuery
     */
    public function homepageDocumentsQuery($params)
    {
        $tableName = $this->tableName();
        $query = $this->baseSearch($params)
            ->where([])
            ->andWhere([
                $tableName . '.status' => Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO,
            ])
            ->andWhere($tableName . '.deleted_at IS NULL')
            ->andWhere($tableName . '.primo_piano = 1');
        return $query;
    }

    /**
     * @param string $queyType
     * @param array $params
     * @return ActiveQuery $query
     */
    public function buildQuery($queyType, $params){

        $query = $this->baseSearch($params);
        $classname = Documenti::className();
        $moduleCwh = \Yii::$app->getModule('cwh');
        $cwhActiveQuery = null;

        $isSetCwh = $this->isSetCwh($moduleCwh, $classname);
        if ($isSetCwh) {
            $moduleCwh->setCwhScopeFromSession();
            $cwhActiveQuery = new \lispa\amos\cwh\query\CwhActiveQuery(
                $classname,[
                'queryBase' => $query
            ]);
        }

        switch($queyType){
            case 'created-by':
                if ($isSetCwh){
                    $query = $cwhActiveQuery->getQueryCwhOwn();
                } else {
                    $query->andFilterWhere([
                        'created_by' => Yii::$app->getUser()->id
                    ]);
                }
                break;
            case 'all':
                if ($isSetCwh){
                    $query = $cwhActiveQuery->getQueryCwhAll();
                } else {
                    $query->andWhere([
                        'status' => Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO,
                    ]);
                }
                break;
            case'to-validate':
                if ($isSetCwh){
                    $query = $cwhActiveQuery->getQueryCwhToValidate();
                } else {
                    $query->andWhere([
                        'status' => Documenti::DOCUMENTI_WORKFLOW_STATUS_DAVALIDARE
                    ]);
                }
                break;
            case 'own-interest':
                if ($isSetCwh){
                    $query = $cwhActiveQuery->getQueryCwhOwnInterest();
                } else {
                    $query->andWhere([
                        'status' => Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO,
                    ]);
                }
                break;
            case 'admin-all':
                /*no filter*/
                break;
        }


        return $query;
    }

    /**
     * @param AmosModule $moduleCwh
     * @param string $classname
     * @return bool
     */
    private function isSetCwh($moduleCwh, $classname){
        if (isset($moduleCwh) && in_array($classname, $moduleCwh->modelsEnabled) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $notifier
     */
    public function setNotifier(NotifyWidget $notifier)
    {
        $this->container->set('notify',$notifier);
    }

    /**
     * @return $this
     */
    public function getNotifier()
    {
        return  $this->container->get('notify');
    }


    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchVersions($params)
    {
        $currentDoc = Documenti::find()->select('max(version), id')->andWhere(['OR',
            ['version_parent_id' => $params['parent_id']],
            ['id' => $params['parent_id']],
        ])->one();

        $query = Documenti::find()
            ->andFilterWhere(['version_parent_id' => $params['parent_id']])
            ->andFilterWhere(['!=', 'id', $currentDoc->id])
            ->orderBy('version DESC');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

}
