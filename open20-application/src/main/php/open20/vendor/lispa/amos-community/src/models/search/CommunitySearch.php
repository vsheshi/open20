<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\models\search;

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityType;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\models\CwhAuthAssignment;
use lispa\amos\notificationmanager\base\NotifyWidget;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\di\Container;
use yii\helpers\ArrayHelper;

/**
 * Class CommunitySearch
 * CommunitySearch represents the model behind the search form about `lispa\amos\community\models\Community`.
 * @package lispa\amos\community\models\search
 */
class CommunitySearch extends Community
{
    private $container;
    /** @var bool|false $subcommunityMode - true if navigating child communities of a main community */
    public $subcommunityMode = false;
    
    public function __construct(array $config = [])
    {
        $this->container = new Container();
        $this->container->set('notify', Yii::$app->getModule('notify'));
        parent::__construct($config);
    }
    
    
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'deleted_by', 'community_type_id'], 'integer'],
            [['status', 'name', 'description', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['logo_id', 'cover_image_id'], 'number'],
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
        $moduleCommunity = \Yii::$app->getModule('community');
        if (isset($moduleTag) && in_array($moduleCommunity->modelMap['Community'], $moduleTag->modelsEnabled) && $moduleTag->behaviors) {
            $behaviors = ArrayHelper::merge($moduleTag->behaviors, $behaviors);
        }
        
        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }
    
    /**
     * @param array $params
     * @return ActiveQuery $query
     */
    public function baseSearch($params)
    {
        //init the default search values
        $this->initOrderVars();
        
        //check params to get orders value
        $this->setOrderVars($params);
        
        $moduleCommunity = Yii::$app->getModule('community');
        
        /** @var Community $className */
        $className = $moduleCommunity->modelMap['Community'];
        return $className::find()->distinct();
    }
    
    /**
     * Search all the communities visible by the logged user
     *
     * @param array $params $_GET search parameters array
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
    public function searchAdminAllCommunities($params, $limit = null)
    {
        return $this->search($params, 'admin-all', $limit);
    }
    
    /**
     * Search for communities whose the logged user belongs
     *
     * @param array $params $_GET search parameters array
     * @param int|null $limit
     * @param bool|false $onlyActiveStatus
     * @return ActiveDataProvider $dataProvider
     */
    public function searchMyCommunities($params, $limit = null, $onlyActiveStatus = false)
    {
        return $this->search($params, 'own-interest', $limit, $onlyActiveStatus);
    }
    
    /**
     * Search for the communities created by the logged user
     *
     * @param array $params $_GET search parameters array
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function searchCreatedByCommunities($params, $limit = null)
    {
        return $this->search($params, 'created-by', $limit);
    }
    
    /**
     * Search for the communities that the logged user has permission to validate
     *
     * @param array $params $_GET search parameters array
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function searchToValidateCommunities($params, $limit = null)
    {
        return $this->search($params, 'to-validate', $limit);
    }
    
    /**
     * @param array $params $_GET search parameters array
     * @param string $queryType , depending on the index tab user is on (communities created by me, my communities, all communities,..)
     * @param bool $onlyActiveStatus
     * @param int|null $limit the query limit
     * @return ActiveDataProvider $dataProvider
     */
    public function search($params, $queryType, $limit = null, $onlyActiveStatus = false)
    {
        $query = $this->buildQuery($queryType, $params, $onlyActiveStatus);
        $query->limit($limit);
        /** @var $notify AmosNotify */
        $notify = Yii::$app->getModule('notify');
        if ($notify) {
            $notify->notificationOff(Yii::$app->getUser()->id, Community::className(), $query, \lispa\amos\notificationmanager\models\NotificationChannels::CHANNEL_READ);
        }
        $dp_params = ['query' => $query,];
        if ($limit) {
            $dp_params ['pagination'] = false;
        }
        $dataProvider = new ActiveDataProvider($dp_params);
        
        //check if can use the custom module order
        if ($this->canUseModuleOrder()) {
            $dataProvider->setSort([
                'defaultOrder' => [
                    $this->orderAttribute => (int)$this->orderType
                ]
            ]);
        } else { //for widget graphic last news, order is incorrect without this else
            $dataProvider->setSort([
                'defaultOrder' => [
                    'created_at' => SORT_DESC
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
                                "entities_tag.classname = '" . addslashes(Community::className()) . "' AND entities_tag.record_id=community.id");
                            
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
            'logo_id' => $this->logo_id,
            'cover_image_id' => $this->cover_image_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        if (!$communityModule->hideCommunityTypeSearchFilter) {
            $query->andFilterWhere(['community_type_id' => $this->community_type_id]);
        }
        
        $query->andFilterWhere(['like', 'community.name', $this->name])
            ->andFilterWhere(['like', 'community.description', $this->description]);
        
        return $dataProvider;
    }
    
    /**
     * @param array $params
     * @param bool|false $onlyActiveStatus
     * @return ActiveQuery $query
     */
    public function buildQuery($queryType, $params, $onlyActiveStatus = false)
    {
        $query = $this->baseSearch($params);
        $userId = Yii::$app->getUser()->getId();
        
        switch ($queryType) {
            case 'created-by':
                $query->andFilterWhere(['community.created_by' => $userId]);
                break;
            case 'all':
                /** @var ActiveQuery $queryClosed */
                $queryClosed = $this->baseSearch($params);
                $queryClosed->innerJoin(CommunityUserMm::tableName(), 'community.id = ' . CommunityUserMm::tableName() . '.community_id'
                    . ' AND ' . CommunityUserMm::tableName() . '.user_id = ' . $userId)
                    ->andFilterWhere([
                        'community.community_type_id' => CommunityType::COMMUNITY_TYPE_CLOSED
                    ])->andWhere(CommunityUserMm::tableName() . '.deleted_at is null');
                $queryClosed->select('community.id');
                $queryNotClosed = $this->baseSearch($params);
                $queryNotClosed->leftJoin(CommunityUserMm::tableName(), 'community.parent_id = ' . CommunityUserMm::tableName() . '.community_id'
                    . ' AND ' . CommunityUserMm::tableName() . '.user_id = ' . $userId)
                    ->andWhere(CommunityUserMm::tableName() . '.deleted_at is null')
                    ->andWhere('community.parent_id is null OR (community.parent_id is not null AND '
                        . CommunityUserMm::tableName() . '.community_id is not null AND '
                        . CommunityUserMm::tableName() . ".status = '" . CommunityUserMm::STATUS_ACTIVE . "')");
                $queryNotClosed->andWhere([
                    'community.community_type_id' => [CommunityType::COMMUNITY_TYPE_OPEN, CommunityType::COMMUNITY_TYPE_PRIVATE]
                ]);
                $queryNotClosed->select('community.id');
                $query->andWhere(['community.id' => $queryClosed])->orWhere(['community.id' => $queryNotClosed]);
                $this->filterValidated($query);
                break;
            case'to-validate':
                $query->andFilterWhere(['community.status' => Community::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE]);
                //check permission to validate a subcommunity
                $subcommunityPermissions = CwhAuthAssignment::find()->andWhere([
                    'cwh_config_id' => self::getCwhConfigId(),
                    'item_name' => "CWH_PERMISSION_VALIDATE_" . Community::className(),
                    'cwh_auth_assignment.user_id' => $userId
                ])->select('cwh_network_id')->column();
                //user with role community_validator can validate root communities too (communities with no parent)
                if (Yii::$app->user->can("COMMUNITY_VALIDATOR")) {
                    $query->andWhere([
                        'or',
                        ['community.parent_id' => null],
                        ['community.parent_id' => $subcommunityPermissions]
                    ]);
                } else { //user does not have persiion to validate root communities, search only for subcommunities user can validate
                    $query->andWhere(['community.parent_id' => $subcommunityPermissions]);
                }
                break;
            case 'own-interest':
                $this->filterValidated($query);
                $query->innerJoin(CommunityUserMm::tableName(), 'community.id = ' . CommunityUserMm::tableName() . '.community_id'
                    . ' AND ' . CommunityUserMm::tableName() . '.user_id = ' . $userId)
                    ->andWhere(CommunityUserMm::tableName() . '.deleted_at is null');
                if ($onlyActiveStatus) {
                    $query->andWhere([CommunityUserMm::tableName() . '.status' => CommunityUserMm::STATUS_ACTIVE]);
                }
                break;
            case 'admin-all':
                /*no filter*/
                break;
        }
        $this->filterByContext($query);
        return $query;
    }
    
    /**
     * @param ActiveQuery $query
     */
    private function filterValidated($query)
    {
        $query->andWhere(['or',
                ['community.status' => Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED],
                ['and',
                    ['community.validated_once' => 1],
                    ['community.visible_on_edit' => 1]
                ]
            ]
        );
    }
    
    /**
     * @param ActiveQuery $query
     */
    private function filterByContext($query)
    {
        if (!empty($query)) {
            $query->andWhere(['community.context' => Community::className()]);
            $query->andWhere('community.deleted_at is null');
        }
        /** @var AmosCommunity $moduleCommunity */
        $moduleCommunity = Yii::$app->getModule('community');
        $showSubscommunities = $moduleCommunity->showSubcommunities;
        if ($this->subcommunityMode) {
            /** @var AmosCwh $moduleCwh */
            $moduleCwh = Yii::$app->getModule('cwh');
            if (!is_null($moduleCwh)) {
                $scope = $moduleCwh->getCwhScope();
                if (!empty($scope) && isset($scope[self::tableName()])) {
                    $communityId = $scope[self::tableName()];
                    //filter by communities chlid of the community ID in cwh scope
                    $query->andWhere(['community.parent_id' => $communityId]);
                    //and show subcommunities in the list anyway (we are in community dashboard)
                    $showSubscommunities = true;
                }
            }
        }
        if (!$showSubscommunities) {
            $query->andWhere(['community.parent_id' => null]);
        }
    }
    
    /**
     * @param $notifier
     */
    public function setNotifier(NotifyWidget $notifier)
    {
        $this->container->set('notify', $notifier);
    }
    
    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getNotifier()
    {
        return $this->container->get('notify');
    }
    
    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchParticipants($params)
    {
        /** @var yii\db\ActiveQuery $query */
        $query = $this->getCommunityUserMms();
        $query->orderBy('user_profile.cognome ASC');
        $participantsDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $participantsDataProvider;
    }
}
