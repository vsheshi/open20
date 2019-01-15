<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\models\search
 * @category   CategoryName
 */

namespace lispa\amos\admin\models\search;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\user\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class UserProfileSearch
 *
 * UserProfileSearch represents the model behind the search form about `common\models\UserProfile`.
 *
 * @property string $email
 *
 * @package lispa\amos\admin\models\search
 */
class UserProfileSearch extends UserProfile
{
    /**
     * @var string $username
     */
    public $username = '';
    
    /**
     * @var string $email
     */
    public $email = '';
    
    /**
     * @var bool $isFacilitator
     */
    public $isFacilitator;
    
    /**
     * @var bool $isOperatingReferent
     */
    public $isOperatingReferent;
    
    /**
     * @var string $userProfileStatus
     */
    public $userProfileStatus = '';
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'privacy'], 'integer'],
            [[
                'nome',
                'cognome',
                'username',
                'email',
                'sesso',
                'prevalent_partnership_id',
                'facilitatore_id',
                'status',
                'isFacilitator',
                'isOperatingReferent',
                'userProfileStatus',
                'validato_almeno_una_volta',
            ], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'userProfileStatus' => AmosAdmin::t('amosadmin', 'Stato profilo utente'),
        ]);
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
     * This is the base search.
     * @param array $params
     * @return ActiveQuery
     */
    public function baseSearch($params)
    {
        /** @var ActiveQuery $query */
        $query = AmosAdmin::instance()->createModel('UserProfile')->find()->innerJoinWith(['user']);
    
        // TODO modificare quando sarà creato il nuovo plugin delle organizzazioni
        if (!is_null(Yii::$app->getModule('organizzazioni'))) {
            $query->joinWith(['prevalentPartnership']);
        }
        
        $cwh = Yii::$app->getModule("cwh");
        // if we are navigating users inside a sprecific entity (eg. a community)
        // see users filtered by entity-user association table
        if (isset($cwh)) {
            $cwh->setCwhScopeFromSession();
            if (!empty($cwh->userEntityRelationTable)) {
                $mmTable = $cwh->userEntityRelationTable['mm_name'];
                $mmTableAlis =  'u2';
                $entityField = $cwh->userEntityRelationTable['entity_id_field'];
                $entityId = $cwh->userEntityRelationTable['entity_id'];
                $query
                    ->innerJoin($mmTable . ' ' .$mmTableAlis, $mmTableAlis . '.user_id = user_profile.user_id ')
                    ->andWhere([
                        $mmTableAlis . '.' . $entityField => $entityId
                    ])->andWhere($mmTableAlis . '.deleted_at is null');
            }
        }
        
        // Init the default search values
        $this->initOrderVars();
        
        // Check params to get orders value
        $this->setOrderVars($params);
        
        return $query;
    }

    /**
     * @param ActiveQuery $query
     * @return mixed
     */
    public function baseFilter($query)
    {
        $query->andFilterWhere([
            UserProfile::tableName() . '.status' => $this->userProfileStatus,
            UserProfile::tableName() . '.validato_almeno_una_volta' => $this->validato_almeno_una_volta,
        ]);
        
        $query->andFilterWhere(['like', UserProfile::tableName() . '.nome', $this->nome])
            ->andFilterWhere(['like', UserProfile::tableName() . '.cognome', $this->cognome])
            ->andFilterWhere(['like', User::tableName() . '.username', $this->username])
            ->andFilterWhere(['like', User::tableName() . '.email', $this->email]);
        
        $this->userProfileSelectFieldsQuery($query, 'prevalent_partnership_id');
        $this->userProfileSelectFieldsQuery($query, 'facilitatore_id');
        
        // If value is "-1" it mean the user is searching whether the sex value is not selected.
        if ($this->sesso == -1) {
            $query->andWhere(['or', [UserProfile::tableName() . '.sesso' => null], [UserProfile::tableName() . '.sesso' => '']]);
        } else {
            $query->andFilterWhere([
                UserProfile::tableName() . '.sesso' => $this->sesso
            ]);
        }
        
        $this->userProfileRolesQuery($query, 'isFacilitator', 'FACILITATOR');
        $this->userProfileRolesQuery($query, 'isOperatingReferent', 'OPERATING_REFERENT');
        
        return $query;
    }
    
    /**
     * @param ActiveQuery $query
     * @param string $fieldName
     */
    protected function userProfileSelectFieldsQuery($query, $fieldName)
    {
        // If value is "-1" it mean the user is searching whether the prevalent partnership is not selected.
        if ($this->{$fieldName} == -1) {
            $query->andWhere([UserProfile::tableName() . '.' . $fieldName => null]);
        } else {
            $query->andFilterWhere([
                UserProfile::tableName() . '.' . $fieldName => $this->{$fieldName}
            ]);
        }
    }
    
    /**
     * This method add a query for a field that filter by a role
     * @param ActiveQuery $query
     * @param string $fieldName
     */
    protected function userProfileRolesQuery($query, $fieldName, $role)
    {
        if ((strlen($this->{$fieldName}) > 0) && (($this->{$fieldName} == 0) || ($this->{$fieldName} == 1))) {
            $operator = ($this->{$fieldName} == 0 ? 'not in' : 'in');
            $userIds = \Yii::$app->getAuthManager()->getUserIdsByRole($role);
            $query->andWhere([$operator, UserProfile::tableName() . '.user_id', $userIds]);
        }
    }
    
    /**
     * @param ActiveDataProvider $dataProvider
     */
    protected function setUserProfileSort($dataProvider)
    {
        // Check if can use the custom module order
        if ($this->canUseModuleOrder()) {
            $dataProvider->setSort([
                'attributes' => [
                    'nome' => [
                        'asc' => ['nome' => SORT_ASC, 'cognome' => SORT_ASC],
                        'desc' => ['nome' => SORT_DESC, 'cognome' => SORT_DESC],
                        'default' => SORT_ASC
                    ],
                    'cognome' => [
                        'asc' => ['cognome' => SORT_ASC, 'nome' => SORT_ASC],
                        'desc' => ['cognome' => SORT_DESC, 'nome' => SORT_DESC],
                        'default' => SORT_ASC
                    ],
                    'surnameName' => [
                        'asc' => ['cognome' => SORT_ASC, 'nome' => SORT_ASC],
                        'desc' => ['cognome' => SORT_DESC, 'nome' => SORT_DESC],
                        'default' => SORT_ASC
                    ],
                    'prevalentPartnership' => [
                        'asc' => ['organizations.name' => SORT_ASC, 'cognome' => SORT_ASC, 'nome' => SORT_ASC],
                        'desc' => ['organizations.name' => SORT_DESC, 'cognome' => SORT_DESC, 'nome' => SORT_DESC],
                        'default' => SORT_ASC
                    ],
                    'created_at'
                ],
                'defaultOrder' => [
                    $this->orderAttribute => (int)$this->orderType
                ]
            ]);
        }
    }
    
    /**
     * Search all active users.
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->baseSearch($params);
        $query->andWhere([UserProfile::tableName() . '.attivo' => 1]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        $this->setUserProfileSort($dataProvider);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $this->baseFilter($query);
        
        return $dataProvider;
    }
    
    /**
     * Search all active users that was validated at least once.
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchOnceValidatedUsers($params)
    {
        $query = $this->baseSearch($params);
        $query->andWhere([UserProfile::tableName() . '.attivo' => 1, UserProfile::tableName() . '.validato_almeno_una_volta' => 1]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        $this->setUserProfileSort($dataProvider);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $this->baseFilter($query);
        
        return $dataProvider;
    }
    
    /**
     * Search all active users that are a community manager for at least one community.
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchCommunityManagerUsers($params)
    {
        $communityModule = Yii::$app->getModule('community');
        if (!is_null($communityModule)) {
            /** @var \lispa\amos\community\AmosCommunity $communityModule */
            $queryAll = $this->baseSearch($params);
            $queryAll->andWhere([UserProfile::tableName() . '.attivo' => 1]);
            $communityTableName = \lispa\amos\community\models\Community::tableName();
            $communityUserMmTableName = \lispa\amos\community\models\CommunityUserMm::tableName();
            $loggedUserId = Yii::$app->getUser()->getId();
            $queryAll->innerJoin($communityUserMmTableName, UserProfile::tableName() . '.user_id = ' . $communityUserMmTableName . '.user_id');
            $queryAll->andWhere([
                $communityUserMmTableName . '.status' => \lispa\amos\community\models\CommunityUserMm::STATUS_ACTIVE,
                $communityUserMmTableName . '.role' => \lispa\amos\community\models\CommunityUserMm::ROLE_COMMUNITY_MANAGER,
				$communityUserMmTableName . '.deleted_at' => NULL,
            ]);
            $queryAll->groupBy(UserProfile::tableName() . '.user_id');
            $allCommunityManagers = $queryAll->all();
            $managerQuery = new Query();
            $managerQuery->select([
                $communityTableName . '.id',
                $communityTableName . '.community_type_id',
                $communityUserMmTableName . '.user_id',
                $communityUserMmTableName . '.role',
            ]);
            $managerQuery->from($communityTableName);
            $managerQuery->innerJoin($communityUserMmTableName, $communityTableName . '.id = ' . $communityUserMmTableName . '.community_id');
            $managerQuery->andWhere([$communityUserMmTableName . '.status' => \lispa\amos\community\models\CommunityUserMm::STATUS_ACTIVE]);
            $managerQuery->andWhere([$communityTableName . '.context' => \lispa\amos\community\models\Community::className()]);
            $managerQuery->andWhere([$communityTableName . '.validated_once' => 1]);
            $communityUserMms = $managerQuery->all();
            
            $managerUserIds = [];
            foreach ($allCommunityManagers as $communityManager) {
                /** @var UserProfile $communityUserMm */
                $managerCommunityIds = [];
                foreach ($communityUserMms as $communityUserMm) {
                    if ($communityManager->user_id == $communityUserMm['user_id']) {
                        $managerCommunityIds[] = $communityUserMm['id'];
                        if ($communityUserMm['community_type_id'] != \lispa\amos\community\models\CommunityType::COMMUNITY_TYPE_CLOSED) {
                            $managerUserIds[] = $communityManager->user_id;
                        }
                    }
                }
                
                // This means that there's only closed communities
                if (!in_array($communityManager->user_id, $managerUserIds)) {
                    foreach ($communityUserMms as $communityUserMm) {
                        if (in_array($communityUserMm['id'], $managerCommunityIds) && ($communityUserMm['user_id'] == $loggedUserId)) {
                            $managerUserIds[] = $communityManager->user_id;
                        }
                    }
                }
            }
            $managerUserIds = array_unique($managerUserIds);
            
            /** @var ActiveQuery $query */
            $query = AmosAdmin::instance()->createModel('UserProfile')->find()->andWhere(['user_id' => $managerUserIds]);
        } else {
            $query = $this->baseSearch($params);
            $query->andWhere([UserProfile::tableName() . '.attivo' => 1]);
            $query->where('0');
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        $this->setUserProfileSort($dataProvider);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $this->baseFilter($query);
        
        return $dataProvider;
    }
    
    /**
     * Search all active users with "FACILITATOR" role.
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchFacilitatorUsers($params)
    {
        $query = $this->baseSearch($params);
        $query->andWhere([UserProfile::tableName() . '.attivo' => 1]);
        $facilitatorUserIds = \Yii::$app->getAuthManager()->getUserIdsByRole('FACILITATOR');
        $query->andWhere(['in', UserProfile::tableName() . '.user_id', $facilitatorUserIds]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        $this->setUserProfileSort($dataProvider);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $this->baseFilter($query);
        
        return $dataProvider;
    }
    
    /**
     * Search all inactive users.
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchInactiveUsers($params)
    {
        $query = $this->baseSearch($params);
        $query->andWhere([UserProfile::tableName() . '.attivo' => 0]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        $this->setUserProfileSort($dataProvider);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $this->baseFilter($query);
        
        return $dataProvider;
    }
    
    /**
     * This method count
     * @return int
     */
    public function getNewProfilesCount()
    {
        /** @var User $loggedUser */
        $loggedUser = Yii::$app->user->identity;
        /** @var UserProfile $loggedUserProfile */
        $loggedUserProfile = $loggedUser->getProfile();
        $query = new Query();
        $query->from(UserProfile::tableName());
        $query->andWhere(['>=', 'created_at', $loggedUserProfile->ultimo_logout]);
        return $query->count();
    }
}
