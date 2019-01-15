<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\models
 * @category   CategoryName
 */

namespace lispa\amos\community\models;

use lispa\amos\attachments\behaviors\FileBehavior;
use lispa\amos\community\AmosCommunity;
use lispa\amos\community\i18n\grammar\CommunityGrammar;
use lispa\amos\community\widgets\icons\WidgetIconCommunityDashboard;
use lispa\amos\community\widgets\UserNetworkWidget;
use lispa\amos\core\interfaces\ContentModelInterface;
use lispa\amos\core\interfaces\ModelLabelsInterface;
use lispa\amos\core\interfaces\ViewModelInterface;
use lispa\amos\core\interfaces\WorkflowModelInterface;
use lispa\amos\core\user\User;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\base\ModelNetworkInterface;
use lispa\amos\cwh\behaviors\TaggableInterestingBehavior;
use lispa\amos\cwh\models\CwhAuthAssignment;
use lispa\amos\cwh\models\CwhConfig;
use lispa\amos\notificationmanager\behaviors\NotifyBehavior;
use lispa\amos\workflow\behaviors\WorkflowLogFunctionsBehavior;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class Community
 * This is the model class for table "community".
 *
 * @property \lispa\amos\community\models\CommunityUserMm[] $communityManagerMms
 * @property \lispa\amos\core\user\User[] $communityManagers
 *
 * @package lispa\amos\community\models
 */
class Community extends \lispa\amos\community\models\base\Community implements CommunityContextInterface, ModelNetworkInterface, WorkflowModelInterface, ViewModelInterface, ModelLabelsInterface, ContentModelInterface
{
    /**
     * @var    string    COMMUNITY_WORKFLOW    Community Workflow ID
     */
    const COMMUNITY_WORKFLOW = 'CommunityWorkflow';
    
    /**
     * @var    string    COMMUNITY_WORKFLOW_STATUS_DRAFT        ID status draft in the model workflow (editing in progress)
     */
    const COMMUNITY_WORKFLOW_STATUS_DRAFT = 'CommunityWorkflow/DRAFT';
    
    /**
     * @var    string    COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE        ID status to be validated in the model workflow
     */
    const COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE = 'CommunityWorkflow/TOVALIDATE';
    
    /**
     * @var    string    COMMUNITY_WORKFLOW_STATUS_VALIDATED        ID status validated in the model workflow
     */
    const COMMUNITY_WORKFLOW_STATUS_VALIDATED = 'CommunityWorkflow/VALIDATED';
    
    /**
     * @var    string    COMMUNITY_WORKFLOW_STATUS_NOT_VALIDATED        ID status not validated in the model workflow
     */
    const COMMUNITY_WORKFLOW_STATUS_NOT_VALIDATED = 'CommunityWorkflow/NOTVALIDATED';
    
    /**
     * @var mixed $communityLogo Community logo.
     */
    public $communityLogo;
    
    /**
     * @var mixed $communityCoverImage Community cover image.
     */
    public $communityCoverImage;
    
    /**
     * @var bool $backToEdit - used in Community form
     * if true, name or description have been modified and community is in published status and community goes back to edit status
     */
    public $backToEdit;

    const ROLE_COMMUNITY_MANAGER = 'COMMUNITY_MANAGER';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        //in creation phase the status is set to initial status defined in community workflow
        if ($this->isNewRecord) {
            $this->status = $this->getWorkflowSource()->getWorkflow(self::COMMUNITY_WORKFLOW)->getInitialStatusId();
            if ($this->status == self::COMMUNITY_WORKFLOW_STATUS_VALIDATED) {
                $this->validated_once = 1;
            }
            $communityModule = Yii::$app->getModule('community');
            if (!is_null($communityModule->communityType)) {
                $this->community_type_id = $communityModule->communityType;
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        
        $this->communityLogo = $this->getCommunityLogo()->one();
        $this->communityCoverImage = $this->getCommunityCoverImage()->one();
    }
    
    /**
     * Getter for $this->communityLogo;
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityLogo()
    {
        return $this->hasOneFile('communityLogo');
    }
    
    /**
     * Getter for $this->communityCoverImage;
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityCoverImage()
    {
        return $this->hasOneFile('communityCoverImage');
    }
    
    /**
     * Ritorna l'url dell'avatar.
     *
     * @param string $dimension Dimensione. Default = small.
     * @return string Ritorna l'url.
     */
    public function getAvatarUrl($dimension = 'small')
    {
        $dimensionsOldVsNew = [
            'small' => 'square_small',
            'medium' => 'square_medium',
            'large' => 'square_large',
        ];
        if (isset($dimensionsOldVsNew[$dimension])) {
            $dimension = $dimensionsOldVsNew[$dimension];
        }
        $url = '/img/img_default.jpg';
        if (!is_null($this->communityLogo)) {
            $url = $this->communityLogo->getUrl($dimension, false, true);
        }
        return $url;
    }
    
    /**
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['communityLogo'], 'file', 'extensions' => 'jpeg, jpg, png, gif', 'minSize' => 1],
            [['communityCoverImage'], 'file', 'extensions' => 'jpeg, jpg, png, gif', 'minSize' => 1],
            [['backToEdit'], 'integer']
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'communityLogo' => AmosCommunity::t('amoscommunity', 'Logo')
        ]);
    }
    
    public function representingColumn()
    {
        return [
            //inserire il campo o i campi rappresentativi del modulo
            'name',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'workflow' => [
                'class' => SimpleWorkflowBehavior::className(),
                'defaultWorkflowId' => self::COMMUNITY_WORKFLOW,
                'propagateErrorsToModel' => true,
            ],
            'NotifyBehavior' => [
                'class' => NotifyBehavior::className(),
                'conditions' => [],
            ],
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ],
            'WorkflowLogFunctionsBehavior' => [
                'class' => WorkflowLogFunctionsBehavior::className(),
            ]
        ]);
        
        $cwhModule = Yii::$app->getModule('cwh');
        $tagModule = Yii::$app->getModule('tag');
        if (isset($cwhModule) && isset($tagModule)) {
            $cwhTaggable = [
                'interestingTaggable' => [
                    'class' => TaggableInterestingBehavior::className(),
                    'tagValueAttribute' => 'id',
                    'tagValuesSeparatorAttribute' => ',',
                    'tagValueNameAttribute' => 'nome',
                ]
            ];
            
            $behaviors = ArrayHelper::merge($behaviors, $cwhTaggable);
        }
        
        return $behaviors;
    }
    
    /**
     * @inheritdoc
     */
    public function getContextRoles()
    {
        $roles = [
            CommunityUserMm::ROLE_PARTICIPANT,
            CommunityUserMm::ROLE_COMMUNITY_MANAGER
        ];
        $moduleCommunity = Yii::$app->getModule('community');
        if ($moduleCommunity->extendRoles) {
            //add additional roles
            $roles = [
                CommunityUserMm::ROLE_READER,
                CommunityUserMm::ROLE_AUTHOR,
                CommunityUserMm::ROLE_EDITOR,
                CommunityUserMm::ROLE_COMMUNITY_MANAGER
            ];
        }
        return $roles;
    }
    
    /**
     * @inheritdoc
     */
    public function getBaseRole()
    {
        $baseRole = CommunityUserMm::ROLE_PARTICIPANT;
        $moduleCommunity = Yii::$app->getModule('community');
        if ($moduleCommunity->extendRoles) {
            $baseRole = CommunityUserMm::ROLE_READER;
        }
        return $baseRole;
    }
    
    /**
     * @inheritdoc
     */
    public function getManagerRole()
    {
        return CommunityUserMm::ROLE_COMMUNITY_MANAGER;
    }
    
    /**
     * @inheritdoc
     */
    public function getRolePermissions($role)
    {
        switch ($role) {
            case CommunityUserMm::ROLE_READER:
                return [];
                break;
            case CommunityUserMm::ROLE_AUTHOR:
                return ['CWH_PERMISSION_VALIDATE'];
                break;
            case CommunityUserMm::ROLE_EDITOR:
                return ['CWH_PERMISSION_CREATE', 'CWH_PERMISSION_VALIDATE'];
                break;
            case CommunityUserMm::ROLE_PARTICIPANT:
                return ['CWH_PERMISSION_CREATE'];
                break;
            case CommunityUserMm::ROLE_COMMUNITY_MANAGER:
                return ['CWH_PERMISSION_CREATE', 'CWH_PERMISSION_VALIDATE'];
                break;
            default:
                return ['CWH_PERMISSION_CREATE'];
                break;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function getCommunityModel()
    {
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getNextRole($role)
    {
        switch ($role) {
            case CommunityUserMm::ROLE_PARTICIPANT:
                return CommunityUserMm::ROLE_COMMUNITY_MANAGER;
                break;
            case CommunityUserMm::ROLE_COMMUNITY_MANAGER:
                return CommunityUserMm::ROLE_PARTICIPANT;
                break;
            default:
                return CommunityUserMm::ROLE_PARTICIPANT;
                break;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function getPluginModule()
    {
        return 'community';
    }
    
    /**
     * @inheritdoc
     */
    public function getPluginController()
    {
        return 'community';
    }
    
    /**
     * @inheritdoc
     */
    public function getRedirectAction()
    {
        return 'update';
    }
    
    public function getAdditionalAssociationTargetQuery($communityId)
    {
        $communityUserMms = CommunityUserMm::find()->andWhere(['community_id' => $communityId]);
        return User::find()->andFilterWhere(['not in', 'id', $communityUserMms->select('user_id')]);
    }
    
    /**
     * Add CWH permissions based on the role for which a permissions array has been specified,
     * Remove CWH permissions on community domain in case of role degradation
     * or delete all permission in case of user-community association deletion
     *
     * @param CommunityUserMm $communityUserMmRow
     * @param bool|false $delete - if true remove all permission (case deletion user-community association)
     */
    public function setCwhAuthAssignments($communityUserMmRow, $delete = false)
    {
        /** @var AmosCwh $cwhModule */
        $cwhModule = Yii::$app->getModule("cwh");
        $cwhNodeId = 'community-' . $this->id;
        $userId = $communityUserMmRow->user_id;
        $cwhConfigId = self::getCwhConfigId();
        $cwhPermissions = CwhAuthAssignment::find()->andWhere([
            'user_id' => $userId,
            'cwh_config_id' => $cwhConfigId,
            'cwh_network_id' => $this->id
        ])->all();

        if ($delete) {
            if (!empty($cwhPermissions)) {
                /** @var CwhAuthAssignment $cwhPermission */
                foreach ($cwhPermissions as $cwhPermission) {
                    $cwhPermission->delete();
                }
            }
        } else {
            $existingPermissions = [];
            foreach ($cwhPermissions as $item) {
                $existingPermissions[$item->item_name] = $item;
            }

            /** @var Community $callingModel */
            $callingModel = Yii::createObject($this->context);
            /** @var array $rolePermissions */
            $rolePermissions = $callingModel->getRolePermissions($communityUserMmRow->role);
            $permissionsToAdd = [];
            if (!is_null($rolePermissions) && count($rolePermissions)) {
                // for each enabled Content model in Cwh
                foreach ($cwhModule->modelsEnabled as $modelClassname) {
                    foreach ($rolePermissions as $permission) {
                        $cwhAuthAssignment = new CwhAuthAssignment();
                        $cwhAuthAssignment->user_id = $userId;
                        $cwhAuthAssignment->item_name = $permission . '_' . $modelClassname;
                        $cwhAuthAssignment->cwh_nodi_id = $cwhNodeId;
                        $cwhAuthAssignment->cwh_config_id = $cwhConfigId;
                        $cwhAuthAssignment->cwh_network_id = $this->id;
                        $permissionsToAdd[$cwhAuthAssignment->item_name] = $cwhAuthAssignment;
                    }
                }
            }
            // if role is CM add permissions of creation/validation of subcommunities of current community (N.B. only for community not created by events/projects)
            if ($this->context == self::className() && $communityUserMmRow->role == CommunityUserMm::ROLE_COMMUNITY_MANAGER) {
                $cwhCreateSubCommunities = new CwhAuthAssignment();
                $cwhCreateSubCommunities->user_id = $userId;
                $cwhCreateSubCommunities->item_name = $cwhModule->permissionPrefix . "_CREATE_" . self::className();
                $cwhCreateSubCommunities->cwh_nodi_id = $cwhNodeId;
                $cwhCreateSubCommunities->cwh_config_id = $cwhConfigId;
                $cwhCreateSubCommunities->cwh_network_id = $this->id;
                $permissionsToAdd[$cwhCreateSubCommunities->item_name] = $cwhCreateSubCommunities;
                $cwhValidateSubCommunities = new CwhAuthAssignment();
                $cwhValidateSubCommunities->user_id = $userId;
                $cwhValidateSubCommunities->item_name = $cwhModule->permissionPrefix . "_VALIDATE_" . self::className();
                $cwhValidateSubCommunities->cwh_nodi_id = $cwhNodeId;
                $cwhValidateSubCommunities->cwh_config_id = $cwhConfigId;
                $cwhValidateSubCommunities->cwh_network_id = $this->id;
                $permissionsToAdd[$cwhValidateSubCommunities->item_name] = $cwhValidateSubCommunities;
            }
            if (!empty($permissionsToAdd)) {
                /** @var CwhAuthAssignment $permissionToAdd */
                foreach ($permissionsToAdd as $key => $permissionToAdd) {
                    //if user has not already the permission for the community , add it to cwh auth assignment
                    if (!array_key_exists($key, $existingPermissions)) {
                        $permissionToAdd->save(false);
                    }
                }
            }
            // check if there are permissions to remove
            if (!empty($existingPermissions)) {
                /** @var CwhAuthAssignment $cwhPermission */
                foreach ($existingPermissions as $key => $cwhPermission) {
                    if (!array_key_exists($key, $permissionsToAdd)) {
                        $cwhPermission->delete();
                    }
                }
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        return Yii::$app->getUser()->id;
    }
    
    /**
     * @inheritdoc
     */
    public function getMmTableName()
    {
        return CommunityUserMm::tableName();
    }
    
    /**
     * @inheritdoc
     */
    public function getMmNetworkIdFieldName()
    {
        return 'community_id';
    }
    
    /**
     * @inheritdoc
     */
    public function getMmUserIdFieldName()
    {
        return 'user_id';
    }
    
    public function getMmClassName()
    {
        return CommunityUserMm::className();
    }
    
    /**
     * @inheritdoc
     */
    public function isNetworkUser($networkId, $userId = null)
    {
        if (!isset($userId)) {
            $userId = $this->getUserId();
        }
        $mmRow = CommunityUserMm::findOne([
            $this->getMmNetworkIdFieldName() => $networkId,
            $this->getMmUserIdFieldName() => $userId,
            'status' => CommunityUserMm::STATUS_ACTIVE
        ]);
        if (!is_null($mmRow)) {
            return true;
        }
        return false;
    }
    
    /**
     * @inheritdoc
     */
    public function isValidated($networkId = null)
    {
        if (!isset($networkId)) {
            $community = $this;
        } else {
            $community = Community::findOne($networkId);
        }
        if (!isset($community) || $community->isNewRecord) {
            return false;
        }
        if ($community->status == self::COMMUNITY_WORKFLOW_STATUS_VALIDATED || ($community->validated_once && $community->visible_on_edit && $community->status == self::COMMUNITY_WORKFLOW_STATUS_DRAFT)) {
            return true;
        }
        return false;
    }
    
    /**
     * @inheritdoc
     */
    public function getToValidateStatus()
    {
        return self::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE;
    }
    
    /**
     * @inheritdoc
     */
    public function getValidatedStatus()
    {
        return self::COMMUNITY_WORKFLOW_STATUS_VALIDATED;
    }
    
    /**
     * @inheritdoc
     */
    public function getDraftStatus()
    {
        return self::COMMUNITY_WORKFLOW_STATUS_DRAFT;
    }
    
    /**
     * @inheritdoc
     */
    public function getValidatorRole()
    {
        return 'COMMUNITY_VALIDATOR';
    }
    
    /**
     * @param int|null $userId - if null the logged user id is considered
     * @return bool
     */
    public function isCommunityManager($userId = null)
    {
        if (!isset($userId)) {
            $userId = \Yii::$app->getUser()->id;
        }
        $managerRole = $this->getManagerRole();
        $communityMm = CommunityUserMm::findOne([
            'community_id' => $this->id,
            'role' => $managerRole,
            'status' => CommunityUserMm::STATUS_ACTIVE,
            'user_id' => $userId
        ]);
        return !is_null($communityMm);
    }

    /**
     * @param int|null $userId - if null the logged user id is considered
     * @return bool
     */
    public function hasRole($userId, $role)
    {
        if (!isset($userId)) {
            $userId = \Yii::$app->getUser()->id;
        }
        $communityMm = CommunityUserMm::findOne([
            'community_id' => $this->id,
            'role' => $role,
            'status' => CommunityUserMm::STATUS_ACTIVE,
            'user_id' => $userId
        ]);
        return !is_null($communityMm);
    }
    
    /**
     * @param null $userId
     * @param bool $isUpdate
     * @return string
     */
    
    public function getUserNetworkWidget($userId = null, $isUpdate = false)
    {
        return UserNetworkWidget::widget(['userId' => $userId, 'isUpdate' => $isUpdate]);
    }
    
    public function getUserNetworkAssociationQuery($userId = null)
    {
        if (empty($userId)) {
            $userId = Yii::$app->user->id;
        }
        
        $mmTable = $this->getMmTableName();
        /** @var ActiveQuery $query */
        $query = self::find()->distinct();
        
        /** @var ActiveQuery $queryJoined */
        $queryJoined = Community::find()->distinct();
        $queryJoined->innerJoin($mmTable,
            'community.id = ' . $mmTable . '.community_id'
            . ' AND ' . $mmTable . '.user_id = ' . $userId)
            ->andWhere($mmTable . '.deleted_at is null');
        $queryJoined->select('community.id');
        
        /** @var ActiveQuery $queryNotClosed */
        $queryNotClosed = self::find()->distinct();
        $queryNotClosed->leftJoin($mmTable,
            'community.parent_id = ' . $mmTable . '.community_id'
            . ' AND ' . $mmTable . '.user_id = ' . $userId)
            ->andWhere($mmTable . '.deleted_at is null')
            ->andWhere([
                'or',
                ['community.parent_id' => null],
                [
                    'and',
                    ['not', ['community.parent_id' => null]],
                    ['not', [$mmTable . '.community_id' => null]],
                    [$mmTable . '.status' => CommunityUserMm::STATUS_ACTIVE],
                ]
            ]);
        $queryNotClosed->andWhere([
            'community.community_type_id' => [CommunityType::COMMUNITY_TYPE_OPEN, CommunityType::COMMUNITY_TYPE_PRIVATE]
        ]);
        $queryNotClosed->select('community.id');
        $query->andWhere(['community.id' => $queryNotClosed])->andWhere(['not in', 'community.id', $queryJoined]);
        $query->andWhere([
            'or',
            ['community.status' => self::COMMUNITY_WORKFLOW_STATUS_VALIDATED],
            [
                'and',
                ['community.visible_on_edit' => 1],
                ['community.validated_once' => 1]
            ]
        ]);
        $query->andWhere(['community.context' => self::className()]);
        $query->andWhere('community.deleted_at is null');
        
        return $query;
    }
    
    /**
     * @param $communityId
     * @return ActiveQuery
     */
    public function getAssociationTargetQuery($communityId)
    {
        $this->id = $communityId;
        $userCommunityIds = $this->getCommunityUserMms()->select('user_profile.user_id');
        /** @var ActiveQuery $userQuery */
        $userQuery = User::find()->andFilterWhere(['not in', User::tableName() . '.id', $userCommunityIds]);
        $userQuery->joinWith('userProfile');
        $userQuery->andWhere('user_profile.id is not null');

//        //it is possible to invite a never validated user only in case the community is type 3 (reserved to members)
//        $community = Community::findOne($communityId);
//        if ($community->community_type_id != CommunityType::COMMUNITY_TYPE_CLOSED) {
//            $userQuery->andWhere(['user_profile.validato_almeno_una_volta' => 1]);
//        }
        $userQuery->andWhere(['user_profile.attivo' => 1]);
        
        $userQuery->orderBy(['cognome' => SORT_ASC, 'nome' => SORT_ASC]);
        return $userQuery;
    }
    
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->name;
    }
    
    /**
     * @return string
     */
    public function getDescription($truncate)
    {
        $ret = $this->description;
        
        if ($truncate) {
            $ret = $this->__shortText($this->description, 200);
        }
        return $ret;
    }
    
    /**
     * @return array
     */
    public function getValidatorUsersId()
    {
        $users = [];
        
        try {
            $authManager = Yii::$app->getAuthManager();
            $users = $authManager->getUserIdsByRole('COMMUNITY_VALIDATOR', true);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $users;
    }
    
    /**
     * @return string The url to view a single model
     */
    public function getViewUrl()
    {
        return "/community/community/view";
    }
    
    /**
     * @return string The url to view of this model
     */
    public function getFullViewUrl()
    {
        return Url::toRoute([$this->getViewUrl(), "id" => $this->id]);
    }
    
    /**
     * @return mixed
     */
    public function getGrammar()
    {
        return new CommunityGrammar();
    }
    
    /**
     * @return array The columns ti show as default in GridViewWidget
     */
    public function getGridViewColumns()
    {
        // TODO: Implement getGridViewColumns() method.
        return [];
    }
    
    /**
     * @return string|null date begin of publication
     */
    public function getPublicatedFrom()
    {
        return null;
    }
    
    /**
     * @return string|null date end of publication
     */
    public function getPublicatedAt()
    {
        return null;
    }
    
    /**
     * @return \yii\db\ActiveQuery category of content
     */
    public function getCategory()
    {
        return null;
    }
    
    /**
     * @return string The classname of the generic dashboard widget to access the plugin
     */
    public function getPluginWidgetClassname()
    {
        return WidgetIconCommunityDashboard::className();
    }
    
    /**
     * @return bool
     */
    public function sendNotification()
    {
        $ret = false;
        
        if ($this->context == self::className()) {
            $ret = true;
        }
        return $ret;
    }
    
    /**
     * Get Id of configuration record for network model Community
     * @return int $cwhConfigId
     */
    public static function getCwhConfigId()
    {
        //default newtwork configuration id = 3 for community
        $cwhConfigId = 3;
        $cwhConfig = CwhConfig::findOne(['tablename' => self::tableName()]);
        if (!is_null($cwhConfig)) {
            $cwhConfigId = $cwhConfig->id;
        }
        return $cwhConfigId;
    }
    
    /**
     * @return array list of statuses that for cwh is validated
     */
    public function getCwhValidationStatuses()
    {
        return [$this->getValidatedStatus()];
    }
    
    /**
     * Query for communities in user network.
     * @param int|null $userId - if null the logged userId is considered.
     * @return ActiveQuery
     */
    public function getUserNetworkQuery($userId = null)
    {
        if (is_null($userId)) {
            $userId = Yii::$app->user->id;
        }
        /** @var ActiveQuery $communities */
        $communities = Community::find()->innerJoin(CommunityUserMm::tableName(),
            CommunityUserMm::tableName() . '.user_id =' . $userId
            . " AND " . CommunityUserMm::tableName() . '.community_id = community.id')
            ->andWhere(['community.context' => Community::className()])
            ->andWhere(CommunityUserMm::tableName() . '.deleted_at IS NULL')
            ->andWhere('community.deleted_at IS NULL');
        $communities->andWhere([
            'or',
            ['community.status' => Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED],
            ['community.validated_once' => 1],
            // The invited user may accept/reject invitation even if community status is not validated and visible on edit flag is not set
            //            ['and',
            //                ['community.validated_once' => 1],
            //                ['community.visible_on_edit' => 1]
            //            ]
        ]);
        return $communities;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityManagerMms()
    {
        return $this->getCommunityUserMms()
            ->andWhere([\lispa\amos\community\models\CommunityUserMm::tableName() . '.status' => \lispa\amos\community\models\CommunityUserMm::STATUS_ACTIVE])
            ->andWhere([\lispa\amos\community\models\CommunityUserMm::tableName() . '.role' => \lispa\amos\community\models\CommunityUserMm::ROLE_COMMUNITY_MANAGER]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityManagers()
    {
        return $this->hasMany(\lispa\amos\core\user\User::className(), ['id' => 'user_id'])->via('communityManagerMms');
    }
}
