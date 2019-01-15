<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\news\models\search
 * @category   CategoryName
 */

namespace lispa\amos\news\models\search;

use lispa\amos\news\AmosNews;
use lispa\amos\news\models\News;
use lispa\amos\notificationmanager\AmosNotify;
use lispa\amos\notificationmanager\base\NotifyWidget;
use lispa\amos\notificationmanager\models\NotificationChannels;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\di\Container;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * NewsSearch represents the model behind the search form about `lispa\amos\news\models\News`.
 */
class NewsSearch extends News
{
    private $container;
    public $data_pubblicazione_filter;

    public function __construct(array $config = [])
    {
        $this->container = new Container();
        $this->container->set('notify', Yii::$app->getModule('notify'));
        parent::__construct($config);
    }


    /**
     */
    public function rules()
    {
        return [
            [['id', 'primo_piano', 'hits', 'abilita_pubblicazione', 'news_categorie_id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['titolo', 'sottotitolo', 'descrizione_breve', 'descrizione', 'metakey', 'metadesc', 'data_pubblicazione', 'data_pubblicazione_filter', 'data_rimozione', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        if (isset($moduleTag) && in_array(News::className(), $moduleTag->modelsEnabled) && $moduleTag->behaviors) {
            $behaviors = ArrayHelper::merge($moduleTag->behaviors, $behaviors);
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * Method that searches for news created by the logged user
     *
     * @param array $params
     * @param int $limit
     * @param boolean $only_drafts
     * @return ActiveDataProvider
     */
    public function searchOwnNews($params, $limit = null, $only_drafts = false)
    {
        return $this->search($params, $limit, "created-by", $only_drafts);
    }

    /**
     * Search method useful to retrieve news.
     *
     * @param $params
     * @param int|null $limit
     * @param string|null $type
     * @param bool $only_drafts
     * @return ActiveDataProvider
     */
    public function search($params, $limit = null, $type = null, $only_drafts = false)
    {
        $query = $this->buildQuery($params, $type, $only_drafts);
        $query->limit($limit);

        /** @var  $notify AmosNotify */
        $notify = $this->getNotifier();
        if ($notify) {
            $notify->notificationOff(Yii::$app->getUser()->id, News::className(), $query,
                NotificationChannels::CHANNEL_READ);
        }
        $dp_params = ['query' => $query,];
        if ($limit) {
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

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

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
                                "entities_tag.classname = '" . addslashes(News::className()) . "' AND entities_tag.record_id=news.id");

                        } else {
                            $andWhere .= " OR ";
                        }
                        $andWhere .= "(entities_tag.tag_id in (" . $tagId . ") AND entities_tag.root_id = " . $rootId . " AND entities_tag.deleted_at is null)";
                        $i++;
                    }
                }
                $andWhere .= "";
                if (!empty($andWhere)) {
                    $query->andWhere($andWhere);
                }
            }
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'primo_piano' => $this->primo_piano,
            'hits' => $this->hits,
            'abilita_pubblicazione' => $this->abilita_pubblicazione,
            'news_categorie_id' => $this->news_categorie_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'news.created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'version' => $this->version,
        ]);
        $query->andFilterWhere(['>=', 'data_pubblicazione', $this->data_pubblicazione_filter]);

        $query->andFilterWhere(['like', 'titolo', $this->titolo])
            ->andFilterWhere(['like', 'sottotitolo', $this->sottotitolo])
            ->andFilterWhere(['like', 'descrizione_breve', $this->descrizione_breve])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione])
            ->andFilterWhere(['like', 'metakey', $this->metakey])
            ->andFilterWhere(['like', 'metadesc', $this->metadesc]);

        return $dataProvider;
    }

    /**
     * Construct query to pass to the data provider to vie a list of news, depending on the index tab $type
     *
     * @param array $params $_GET search parameters
     * @param string $type Depending on the index tab calling the search methods (tab created-by, tab to-validate, tab all,...)
     * @param bool|false $only_drafts
     * @return ActiveQuery $query
     */
    public function buildQuery($params, $type, $only_drafts = false)
    {
        try {
            //common parameters
            $userProfileId = Yii::$app->getUser()->id;

            /** @var ActiveQuery $query */
            $query = $this->baseSearch($params);

            /** @var string $classname News className to check cwh */
            $classname = News::className();

            /** @var AmosCwh $moduleCwh */
            $moduleCwh = Yii::$app->getModule('cwh');

            /** @var CwhActiveQuery $cwhActiveQuery */
            $cwhActiveQuery = null;

            $isSetCwh = $this->isSetCwh($moduleCwh, $classname);
            if ($isSetCwh) {
                $moduleCwh->setCwhScopeFromSession();
                $cwhActiveQuery = new \lispa\amos\cwh\query\CwhActiveQuery($classname, [
                    'queryBase' => $query,
                ]);
            }
            //composes the query based on the type of news requests
            switch ($type) {
                case 'created-by':
                    if ($isSetCwh) {
                        $query = $cwhActiveQuery->getQueryCwhOwn();
                    } else {
                        //user filter
                        $query->andFilterWhere([
                            'created_by' => $userProfileId
                        ]);
                        //if it requires only drafts news
                        if ($only_drafts) {
                            $query->andWhere(['status' => News::NEWS_WORKFLOW_STATUS_BOZZA]);
                        }
                    }
                    break;
                case 'to-validate':
                    if ($isSetCwh) {
                        $query = $cwhActiveQuery->getQueryCwhToValidate();
                    } else {
                        $query->andWhere(['status' => News::NEWS_WORKFLOW_STATUS_DAVALIDARE]);

                    }
                    break;
                case 'own-interest':
                    if ($isSetCwh) {
                        $query = $cwhActiveQuery->getQueryCwhOwnInterest();
                    } else {
                        $query->andWhere(['status' => News::NEWS_WORKFLOW_STATUS_VALIDATO]);
                        $now = date('Y-m-d');
                        $query
                            ->andFilterWhere(['<=', 'data_pubblicazione', $now])
                            ->andFilterWhere(['>=', 'data_rimozione', $now]);
                    }

                    break;
                case 'all':
                    if ($isSetCwh) {
                        $query = $cwhActiveQuery->getQueryCwhAll();
                    } else {
                        $query->andWhere(['status' => News::NEWS_WORKFLOW_STATUS_VALIDATO]);
                        $now = date('Y-m-d');
                        $query
                            ->andFilterWhere(['<=', 'data_pubblicazione', $now])
                            ->andFilterWhere(['>=', 'data_rimozione', $now]);
                    }
                    break;
                case 'admin-all':
                    /*no filter*/
                    break;
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $query;
    }

    /**
     * Basic search of news. Returns all the news and not canceled.
     *
     * @param   array $params Parametri
     * @return \yii\db\ActiveQuery
     */
    public function baseSearch($params)
    {
        //init the default search values
        $this->initOrderVars();

        //check params to get orders value
        $this->setOrderVars($params);

        return News::find()->distinct();
    }

    /**
     * @param AmosModule $moduleCwh
     * @param string $classname
     * @return bool
     */
    private function isSetCwh($moduleCwh, $classname)
    {
        if (isset($moduleCwh) && in_array($classname, $moduleCwh->modelsEnabled)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return $this.
     *
     * @return $this
     */
    public function validazioneAbilitata()
    {
        return $this;
    }

    /**
     * Method that searches all news to be validated.
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function searchToValidateNews($params, $limit = null)
    {
        return $this->search($params, $limit, "to-validate");
    }

    /**
     * Method that search the latest research news validated, typically limit is $ 3.
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function ultimeNews($params, $limit = null)
    {
        $dataProvider = $this->searchAll($params, $limit);

        return $dataProvider;
    }

    /**
     * Search method useful to retrieve all non-deleted news.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchAll($params, $limit = null)
    {
        return $this->search($params, $limit, "all");
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchAdminAll($params, $limit = null)
    {
        return $this->search($params, $limit, "admin-all");
    }

    /**
     * Method that searches all the news validated.
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function searchOwnInterest($params, $limit = null)
    {
        return $this->search($params, $limit, "own-interest");
    }

    /**
     * Search method useful to retrieve validated news with both primo_piano and in_evidenza flags = true.
     *
     * @param array $params Array di parametri
     * @return ActiveDataProvider
     */
    public function searchHighlightedAndHomepageNews($params)
    {
        $query = $this->highlightedAndHomepageNewsQuery($params);

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
     * Search method useful to retrieve validated news with primo_piano flag = true.
     *
     * @param array $params Array di parametri
     * @return ActiveDataProvider
     */
    public function searchHomepageNews($params)
    {
        $query = $this->homepageNewsQuery($params);

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
     * get the query used by the related searchHighlightedAndHomepageNews method
     * return just the query in case data provider/query itself needs editing
     *
     * @param array $params
     * @return \yii\db\ActiveQuery
     */
    public function highlightedAndHomepageNewsQuery($params)
    {
        $now = date('Y-m-d');
        $tableName = $this->tableName();
        $query = $this->baseSearch($params)
            ->andWhere([
                $tableName . '.status' => News::NEWS_WORKFLOW_STATUS_VALIDATO,
            ])
            ->andWhere($tableName . '.in_evidenza = 1')
            ->andWhere($tableName . '.primo_piano = 1');

        $query
            ->andFilterWhere(['<=', 'data_pubblicazione', $now])
            ->andFilterWhere(['>=', 'data_rimozione', $now]);
        return $query;
    }

    /**
     * get the query used by the related searchHomepageNews method
     * return just the query in case data provider/query itself needs editing
     *
     * @param array $params
     * @return \yii\db\ActiveQuery
     */
    public function homepageNewsQuery($params)
    {
        $now = date('Y-m-d');
        $tableName = $this->tableName();
        $query = $this->baseSearch($params)
            ->andWhere([
                $tableName . '.status' => News::NEWS_WORKFLOW_STATUS_VALIDATO,
            ])
            ->andWhere($tableName . '.primo_piano = 1');
        $query
            ->andFilterWhere(['<=', 'data_pubblicazione', $now])
            ->andFilterWhere(['>=', 'data_rimozione', $now]);
        return $query;
    }

    /**
     * @param $notifier
     */
    public function setNotifier(NotifyWidget $notifier)
    {
        $this->container->set('notify', $notifier);
    }

    /**
     * @return $this
     */
    public function getNotifier()
    {
        return $this->container->get('notify');
    }

    /**
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(),
            [
                'titolo' => AmosNews::t('amosnews', '#title_search'),
                'sottotitolo' => AmosNews::t('amosnews', 'Sottotitolo'),
                'descrizione' => AmosNews::t('amosnews', '#description_search'),
                'data_pubblicazione_filter' => AmosNews::t('amosnews', 'Data pubblicazione'),
                'created_by' => AmosNews::t('amosnews', '#created_by_search'),
            ]
        );
    }
}
