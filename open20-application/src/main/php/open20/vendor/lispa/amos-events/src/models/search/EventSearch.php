<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\events
 * @category   CategoryName
 */

namespace lispa\amos\events\models\search;

use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\events\models\Event;
use lispa\amos\events\models\EventMembershipType;
use lispa\amos\events\AmosEvents;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use DateTime;

/**
 * Class EventSearch
 * EventSearch represents the model behind the search form about `lispa\amos\events\models\Event`.
 * @package lispa\amos\events\models\search
 */
class EventSearch extends Event
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [
                [
                    'title',
                    'description',
                    'begin_date_hour',
                    'end_date_hour',
                    'event_type_id',
                    'created_at',
                    'updated_at',
                    'deleted_at'
                ],
                'safe'
            ],
            [
                [
                    'begin_date_hour_from',
                    'begin_date_hour_to',
                    'end_date_hour_from',
                    'end_date_hour_to',
                ],
                'safe'
            ],
        ];
    }

    /**
     * @inheritdoc
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
        if (isset($moduleTag) && in_array(Event::className(), $moduleTag->modelsEnabled) && $moduleTag->behaviors) {
            $behaviors = ArrayHelper::merge($moduleTag->behaviors, $behaviors);
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * @param $params
     * @return ActiveQuery $query
     */
    public function baseSearch($params)
    {

        return Event::find()->distinct();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params, $queryType, $limit = null, $management = false)
    {
        $query = $this->buildQuery($queryType, $params, $management);

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
                                "entities_tag.classname = '" . addslashes(Event::className()) . "' AND entities_tag.record_id=event.id");

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

        $query->joinWith('eventType');

        $query->andFilterWhere([
            'id' => $this->id,
            'event_type_id' => $this->event_type_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        $query->andFilterWhere(['like', self::tableName() . '.title', $this->title])
            ->andFilterWhere(['like', self::tableName() . '.description', $this->description]);
        $query->andFilterWhere(['>=', self::tableName() . '.begin_date_hour', $this->begin_date_hour_from])
            ->andFilterWhere(['<=', self::tableName() . '.begin_date_hour', $this->begin_date_hour_to])
            ->andFilterWhere(['>=', self::tableName() . '.end_date_hour', $this->end_date_hour_from])
            ->andFilterWhere(['<=', self::tableName() . '.end_date_hour', $this->end_date_hour_to]);

        // DEVO VEDERE ANCHE GLI EVENTI PASSATI
//		$datetime = new DateTime($this->end_date_hour);
//		$datetime->modify('+1 day');
//        $query->andFilterWhere(['<=', self::tableName() . '.end_date_hour', $datetime->format('Y-m-d H:i:s')]);
        return $dataProvider;
    }
	
	
	public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'begin_date_hour' => AmosEvents::t('amosevents', '#search_begin_date'),
            'end_date_hour' => AmosEvents::t('amosevents', '#search_end_date'),
        ]);
    }

    /**
     * Search for events created by the logged user
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchCreatedBy($params, $limit = null)
    {
        return $this->search($params, 'created-by', $limit);
    }

    /**
     * @param ActiveQuery $query
     */
    private function filterByMembershipType($query)
    {
        $loggedUserId = Yii::$app->getUser()->id;
        $query->leftJoin('community_user_mm', 'community_user_mm.community_id = event.community_id AND community_user_mm.user_id=' . $loggedUserId);
        $query->andWhere('event.event_membership_type_id !=' . EventMembershipType::TYPE_ON_INVITATION . ' OR 
        ( event.event_membership_type_id = '. EventMembershipType::TYPE_ON_INVITATION . ' AND community_user_mm.user_id = '.$loggedUserId .' AND community_user_mm.deleted_at is null )
         OR  ( event.event_management = 0 AND event.event_membership_type_id is null )');
    }

    /**
     * Search for events visible by the logged user and published on the calendar
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchCalendarView($params, $limit = null)
    {
        return $this->search($params, 'own-interest', $limit);
    }

    /**
     * Search for events that by the logged user has permission to validate
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchToPublish($params, $limit = null)
    {
        return $this->search($params, 'to-validate', $limit);
    }

    /**
     * Search for events where the the logged user is part of the staff
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchManagement($params, $limit = null)
    {
        return $this->search($params, 'all', $limit);
    }

    /**
     * @param string $queryType
     * @param array $params
     * @return ActiveQuery $query
     */
    public function buildQuery($queryType, $params, $management = false)
    {
        $query = $this->baseSearch($params);
        $classname = Event::className();
        /** @var  \lispa\amos\cwh\AmosCwh $moduleCwh */
        $moduleCwh = \Yii::$app->getModule('cwh');
        $cwhActiveQuery = null;

        $isSetCwh = isset($moduleCwh) && in_array($classname, $moduleCwh->modelsEnabled);
        if ($isSetCwh) {
            $moduleCwh->setCwhScopeFromSession();
            $cwhActiveQuery = new \lispa\amos\cwh\query\CwhActiveQuery(
                $classname, [
                'queryBase' => $query
            ]);
        }
        switch ($queryType) {
            case 'created-by':
                if ($isSetCwh) {
                    $query = $cwhActiveQuery->getQueryCwhOwn();
                } else {
                    $query->andFilterWhere([
                        self::tableName() . '.created_by' => Yii::$app->getUser()->id
                    ]);
                }
                break;
            case 'all':
                if ($isSetCwh) {
                    $query = $cwhActiveQuery->getQueryCwhAll();
                } else {
                    $query->andWhere([
                        self::tableName() . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
                    ]);
                }
                break;
            case'to-validate':
                if ($isSetCwh) {
                    $query = $cwhActiveQuery->getQueryCwhToValidate();
                    $this->filterByMembershipType($query);
                }
                $query->andWhere([
                    self::tableName() . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHREQUEST,
                ]);
                break;
            case 'own-interest':
                if ($isSetCwh) {
                    $query = $cwhActiveQuery->getQueryCwhOwnInterest();
                    $this->filterByMembershipType($query);
                    $query->andFilterWhere([
                        'validated_at_least_once' => true,
                        'publish_in_the_calendar' => true,
                        'visible_in_the_calendar' => true
                    ]);
                } else {
                    $query->andWhere([
                        self::tableName() . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
                    ]);
                }
                break;
        }
        if($management){
            $query->joinWith('communityUserMm');

            // MANAGEMENT
            $query->andWhere([
                'community_user_mm.user_id' => \Yii::$app->getUser()->id,
                'community_user_mm.status' => CommunityUserMm::STATUS_ACTIVE,
                'community_user_mm.role' => self::EVENT_MANAGER
            ]);
            $query->andFilterWhere([
                'validated_at_least_once' => true,
                'event_management' => true,
            ]);
        }

        return $query;
    }

    
    /**
     * Search method useful to retrieve events in validated status with both flags publish_in_the_calendar and visible_in_the_calendar true
     *
     * @param array $params Array di parametri
     * @return ActiveDataProvider
     */
    public function searchHighlightedAndHomepageEvents($params)
    {
        $query = $this->highlightedAndHomepageEventsQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'publication_date_begin' => SORT_DESC,
                ],
            ],
        ]);

        return $dataProvider;
    }
    
    /**
     * @param array $params
     * @return \yii\db\ActiveQuery
     */
    public function highlightedAndHomepageEventsQuery($params)
    {
        $tableName = $this->tableName();
        $query = $this->baseSearch($params)
            ->where([])
            ->andWhere([
                $tableName . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
            ])
            ->andWhere($tableName . '.deleted_at IS NULL')
            ->andWhere($tableName . '.publish_in_the_calendar = 1')
            ->andWhere($tableName . '.visible_in_the_calendar = 1');
        return $query;
    }
}
