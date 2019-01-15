<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

namespace lispa\amos\discussioni\models\search;

use lispa\amos\core\module\AmosModule;
use lispa\amos\discussioni\models\DiscussioniTopic;
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
 * DiscussioniTopicSearch represents the model behind the search form about `lispa\amos\discussioni\models\DiscussioniTopic`.
 */
class DiscussioniTopicSearch extends DiscussioniTopic
{
    private $container;

    public function __construct(array $config = [])
    {
        $this->container = new Container();
        $this->container->set('notify',Yii::$app->getModule('notify'));
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['titolo', 'testo', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

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
        if (isset($moduleTag) && in_array(DiscussioniTopic::className(), $moduleTag->modelsEnabled) && $moduleTag->behaviors) {
            $behaviors = ArrayHelper::merge($moduleTag->behaviors, $behaviors);
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function baseSearch($params)
    {
        //init the default search values
        $this->initOrderVars();

        //check params to get orders value
        $this->setOrderVars($params);

        $query = DiscussioniTopic::find()->distinct();
        return $query;
    }

    /**
     * @param array $params
     * @param string $queryType
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function search($params, $queryType, $limit = null)
    {

        $query = $this->buildQuery($queryType, $params);
        $query->limit($limit);
        //Switch off notification service for not readed discussion notifications
        /** @var  $notify AmosNotify */
        $notify = $this->getNotifier();
        if($notify)
        {
            $this->getNotifier();
            $notify->notificationOff(Yii::$app->getUser()->id, DiscussioniTopic::className(),$query,NotificationChannels::CHANNEL_READ);
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
            $sort = $dataProvider->getSort();
        } else { //for widget graphic last news, order is incorrect without this else
            $dataProvider->setSort([
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]);
        }

        /** filter by selected tag values (OR condition)  */
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
                                "entities_tag.classname = '" . addslashes(DiscussioniTopic::className()) . "' AND entities_tag.record_id=discussioni_topic.id");

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

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'deleted_at' => $this->deleted_at,
            'discussioni_topic.created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'version' => $this->version,
        ]);

        if (!empty($this->created_at)) {
            $time = strtotime($this->created_at);
            $createdAtNewformat = date('Y-m-d', $time);
            $query->andFilterWhere([
                '=',
                new Expression('DATE(discussioni_topic.created_at)'),
                $createdAtNewformat
            ]);
        }

        if (!empty($this->updated_at)) {
            $time = strtotime($this->updated_at);
            $updatedAtNewformat = date('Y-m-d', $time);
            $query->andFilterWhere([
                '=',
                new Expression('DATE(discussioni_topic.updated_at)'),
                $updatedAtNewformat
            ]);
        }

        $query->andFilterWhere(['like', 'titolo', $this->titolo])
            ->andFilterWhere(['like', 'testo', $this->testo]);

        return $dataProvider;
    }

    /**
     * @param string $queryType
     * @param array $params
     * @return ActiveQuery $query
     */
    public function buildQuery($queryType, $params){

        $query = $this->baseSearch($params);

        $classname = DiscussioniTopic::className();
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

        switch($queryType){
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
                        'status' => DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_ATTIVA
                    ]);
                }
                break;
            case'to-validate':
                if ($isSetCwh){
                    $query = $cwhActiveQuery->getQueryCwhToValidate();
                } else {
                    $query->andWhere([
                        'status' => DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_DAVALIDARE
                    ]);
                }
                break;
            case 'own-interest':
                if ($isSetCwh){
                    $query = $cwhActiveQuery->getQueryCwhOwnInterest();
                } else {
                    $query->andWhere([
                        'status' => DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_ATTIVA
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
     * Search discussions created by logged user
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchCreatedBy($params, $limit = null)
    {
        return $this->search($params, 'created-by', $limit);
    }

    /**
     * Search discussions in 'to validate' that logged user has permission to validate
     * @param $params
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function searchToValidate($params, $limit = null)
    {
        return $this->search($params, 'to-validate', $limit);
    }

    /**
     * Search all discussions in status validated
     * @param $params
     * @param null $limit
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
     * Search discussion in status validated matching logged user parameters (based on publication rule)
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchOwnInterest($params, $limit = null)
    {
        return $this->search($params, 'own-interest', $limit);
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function ultimeDiscussioni($params, $limit = null)
    {
        // solo le discussioni attive
        $dataProvider = $this->searchAll($params, $limit);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function discussioniInEvidenza($params, $limit = null)
    {
        $query = $this->searchAll($params, $limit)->query;
        $query->andFilterWhere([
            'in_evidenza' => true
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => $limit,
            ]
        ]);

        return $dataProvider;
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

}
