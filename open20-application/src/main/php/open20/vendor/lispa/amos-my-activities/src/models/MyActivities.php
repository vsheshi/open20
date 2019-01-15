<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\models
 * @category   CategoryName
 */

namespace lispa\amos\myactivities\models;

use lispa\amos\admin\models\base\UserProfile;
use lispa\amos\admin\models\UserContact;
use lispa\amos\myactivities\AmosMyActivities;
use lispa\amos\myactivities\basic\CommunityToValidate;
use lispa\amos\myactivities\basic\DiscussionToValidate;
use lispa\amos\myactivities\basic\DocumentToValidate;
use lispa\amos\myactivities\basic\ExpressionOfInterestToEvaluate;
use lispa\amos\myactivities\basic\MyActivitiesList;
use lispa\amos\myactivities\basic\NewsToValidate;
use lispa\amos\myactivities\basic\OrganizationsToValidate;
use lispa\amos\myactivities\basic\PartnershipProfileToValidate;
use lispa\amos\myactivities\basic\ReportToRead;
use lispa\amos\myactivities\basic\RequestToParticipateCommunity;
use lispa\amos\myactivities\basic\RequestToParticipateCommunityForManager;
use lispa\amos\myactivities\basic\ShowcaseProjectToValidate;
use lispa\amos\myactivities\basic\UserProfileToValidate;
use lispa\amos\myactivities\basic\WaitingContacts;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class MyActivities
 * @package lispa\amos\myactivities\models
 */
class MyActivities extends Model
{
    /** @var  $myActivitiesList MyActivitiesList */
    private $myActivitiesList;

    /**
     * @return int
     */
    public static function getCountActivities()
    {
        $model = new MyActivities;
        return count($model->getMyActivities());
    }

    /**
     * @return array
     */
    public function getMyActivities($modelSearch = null)
    {
        $this->myActivitiesList->addModelSet($this->getWaitingContacts());
        $this->myActivitiesList->addModelSet($this->getNewsToValidate());
        $this->myActivitiesList->addModelSet($this->getRequestToParticipateCommunity());
        $this->myActivitiesList->addModelSet($this->getUserProfileToValidate());
        $this->myActivitiesList->addModelSet($this->getComunityToValidate());
        $this->myActivitiesList->addModelSet($this->getDiscussionToValidate());
        $this->myActivitiesList->addModelSet($this->getDocumentToValidate());
        $this->myActivitiesList->addModelSet($this->getOrganizationsToValidate());
        $this->myActivitiesList->addModelSet($this->getShowcaseProjectToValidate());
        $this->myActivitiesList->addModelSet($this->getPartnershipProfilesToValidate());

        /* NOT IMPLEMENTED
        $this->myActivitiesList->addModelSet($this->getSurveyToValidate());
        $this->myActivitiesList->addModelSet($this->getExpressionOfInterestToEvaluate());
        */

        $this->myActivitiesList->addModelSet($this->getRequestToParticipateCommunityForManager());
        $this->myActivitiesList->addModelSet($this->getReportToRead());


        $this->myActivitiesList->applySort($modelSearch);
        $this->myActivitiesList->applyFilter($modelSearch);

        return $this->myActivitiesList->getMyActivitiesList();
    }

    /**
     * @return array
     */
    private function getWaitingContacts()
    {
        if (Yii::$app->hasModule('admin')) {
            $elementList = WaitingContacts::find()
                ->innerJoinWith('user')
                ->andWhere([WaitingContacts::tableName() . '.contact_id' => Yii::$app->user->id])
                ->andWhere([WaitingContacts::tableName() . '.status' => UserContact::STATUS_INVITED])
                ->all();
            return $elementList;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getNewsToValidate()
    {
        if (Yii::$app->hasModule('news')) {
            $modelSearch = new NewsToValidate();
            /** @var ActiveDataProvider $dataProvider */
            $dataProvider = $modelSearch->searchToValidateNews(Yii::$app->request->getQueryParams());
            $ids = ArrayHelper::map($dataProvider->models, 'id', 'id');
            $modelList = NewsToValidate::find()->andWhere(['id' => $ids])->all();
            return $modelList;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getRequestToParticipateCommunity()
    {
        if (Yii::$app->hasModule('community')) {
            $elementList = RequestToParticipateCommunity::find()
                ->joinWith('community')
                ->andWhere(['community.validated_once' => 1])
                ->andWhere(['community_user_mm.user_id' => Yii::$app->user->id])
                ->andWhere(['community_user_mm.status' => \lispa\amos\community\models\CommunityUserMm::STATUS_WAITING_OK_USER])
                ->all();
            return $elementList;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getUserProfileToValidate()
    {
        $elementList = [];

        if (Yii::$app->hasModule('admin')) {
            if (Yii::$app->user->can('ADMIN') || Yii::$app->user->can('FACILITATOR')) {
                $elementList = UserProfileToValidate::find()
                    ->andWhere(['facilitatore_id' => Yii::$app->user->id])
                    ->andWhere(['status' => \lispa\amos\admin\models\UserProfile::USERPROFILE_WORKFLOW_STATUS_TOVALIDATE])
                    ->andWhere(['attivo' => 1])
                    ->all();
            } else {
                if (Yii::$app->user->can('VALIDATOR')) {
                    $elementList = UserProfileToValidate::find()
                        ->andWhere(['status' => \lispa\amos\admin\models\UserProfile::USERPROFILE_WORKFLOW_STATUS_TOVALIDATE])
                        ->andWhere(['attivo' => 1])
                        ->all();
                }
            }
            return $elementList;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getComunityToValidate()
    {
        if (Yii::$app->hasModule('community')) {
            $communitySearch = new CommunityToValidate;
            $dataProvider = $communitySearch->searchToValidateCommunities(Yii::$app->request->getQueryParams());
            $ids = ArrayHelper::map($dataProvider->models, 'id', 'id');
            $modelList = CommunityToValidate::find()->andWhere(['id' => $ids])->all();
            return $modelList;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getDiscussionToValidate()
    {
        if (Yii::$app->hasModule('discussioni')) {
            $modelSearch = new DiscussionToValidate;
            $notifyModule = \Yii::$app->getModule('notify');
            if($notifyModule){
                $modelSearch->setNotifier(new \lispa\amos\notificationmanager\base\NotifyWidgetDoNothing());
            }
            $dataProvider = $modelSearch->searchToValidate(Yii::$app->request->getQueryParams());
            $ids = ArrayHelper::map($dataProvider->models, 'id', 'id');
            $modelList = DiscussionToValidate::find()->andWhere(['id' => $ids])->all();
            return $modelList;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getDocumentToValidate()
    {
        if (Yii::$app->hasModule('documenti')) {
            $modelSearch = new DocumentToValidate();
            $dataProvider = $modelSearch->searchToValidateDocuments(Yii::$app->request->getQueryParams());
            $ids = ArrayHelper::map($dataProvider->models, 'id', 'id');
            $modelList = DocumentToValidate::find()->andWhere(['id' => $ids])->all();
            return $modelList;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getOrganizationsToValidate()
    {
        if (Yii::$app->hasModule('organizations')) {
            $modelSearch = new OrganizationsToValidate();
            $dataProvider = $modelSearch->searchToValidateOrganizations(Yii::$app->request->getQueryParams());
            $ids = ArrayHelper::map($dataProvider->models, 'id', 'id');
            $modelList = OrganizationsToValidate::find()->andWhere(['id' => $ids])->all();
            return $modelList;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getRequestToParticipateCommunityForManager()
    {
        if (Yii::$app->hasModule('community')) {
            $communityModule = Yii::$app->getModule('community');
            $communitiesIds = $communityModule->getCommunitiesByUserId(Yii::$app->user->id, true);
            if (count($communitiesIds) > 0) {
                $elementList = RequestToParticipateCommunityForManager::find()
                    ->innerJoin(UserProfile::tableName(), UserProfile::tableName() . '.user_id = ' . RequestToParticipateCommunityForManager::tableName() . '.user_id and ' . UserProfile::tableName() . '.deleted_at is NULL')
                    ->andWhere(['community_id' => $communitiesIds])
                    ->andWhere([RequestToParticipateCommunityForManager::tableName() . '.status' => \lispa\amos\community\models\CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER])
                    ->all();
                return $elementList;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getReportToRead()
    {
        if (Yii::$app->hasModule('report')) {
            $reportMyInterest = [];
            $reportModule = Yii::$app->getModule('report');
            $ids = $reportModule->getOwnUnreadReports()->select('report.id');
            $allReport = ReportToRead::find()->andWhere(['id' => $ids])->andWhere(['read_at' => null])->all();
            foreach ($allReport as $report) {

                if (Yii::$app->hasModule('news') && ($report->classname == \lispa\amos\news\models\News::className())) {
                    $model = \lispa\amos\news\models\News::find()->andWhere(['id' => $report->context_id])->one();
                    if (!empty($model)) {
                        // Check if user logged is creator
                        if ($model->created_by == Yii::$app->user->id) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                        // Check if user logged is facilitator
                        /** @var UserProfile $userProfileReport */
                        $userProfileReport = UserProfile::find()->andWhere(['user_id' => $model->created_by])->one();
                        if ($userProfileReport->facilitatore_id == Yii::$app->user->id) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                        // Check if user logged is validator
                        $valUserId = $model->getStatusLastUpdateUser(\lispa\amos\news\models\News::NEWS_WORKFLOW_STATUS_VALIDATO);
                        if (!is_null($valUserId) && ($valUserId == Yii::$app->user->id)) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                    }
                }

                if (Yii::$app->hasModule('discussioni') && ($report->classname == \lispa\amos\discussioni\models\DiscussioniTopic::className())) {
                    $model = \lispa\amos\discussioni\models\DiscussioniTopic::find()->andWhere(['id' => $report->context_id])->one();
                    if (!empty($model)) {
                        // Check if user logged is creator
                        if ($model->created_by == Yii::$app->user->id) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                        // Check if user logged is facilitator
                        /** @var UserProfile $userProfileReport */
                        $userProfileReport = UserProfile::find()->andWhere(['user_id' => $model->created_by])->one();
                        if ($userProfileReport->facilitatore_id == Yii::$app->user->id) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                        // Check if user logged is validator
                        $valUserId = $model->getStatusLastUpdateUser(\lispa\amos\discussioni\models\DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_ATTIVA);
                        if (!is_null($valUserId) && ($valUserId == Yii::$app->user->id)) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                    }
                }

                if (Yii::$app->hasModule('documenti') && ($report->classname == \lispa\amos\documenti\models\Documenti::className())) {
                    $model = \lispa\amos\documenti\models\Documenti::find()->andWhere(['id' => $report->context_id])->one();
                    if (!empty($model)) {
                        // Check if user logged is creator
                        if ($model->created_by == Yii::$app->user->id) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                        // Check if user logged is facilitator
                        /** @var UserProfile $userProfileReport */
                        $userProfileReport = UserProfile::find()->andWhere(['user_id' => $model->created_by])->one();
                        if ($userProfileReport->facilitatore_id == Yii::$app->user->id) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                        // Check if user logged is validator
                        $valUserId = $model->getStatusLastUpdateUser(\lispa\amos\documenti\models\Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO);
                        if (!is_null($valUserId) && ($valUserId == Yii::$app->user->id)) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                    }
                }

                if (Yii::$app->hasModule('community') && ($report->classname == \lispa\amos\community\models\Community::className())) {
                    $model = \lispa\amos\community\models\Community::find()->andWhere(['id' => $report->context_id])->one();
                    if (!empty($model)) {
                        // Check if user logged is creator
                        if ($model->created_by == Yii::$app->user->id) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                        // Check if user logged is facilitator
                        /** @var UserProfile $userProfileReport */
                        $userProfileReport = UserProfile::find()->andWhere(['user_id' => $model->created_by])->one();
                        if ($userProfileReport->facilitatore_id == Yii::$app->user->id) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                        // Check if user logged is validator
                        $valUserId = $model->getStatusLastUpdateUser(\lispa\amos\community\models\Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED);
                        if (!is_null($valUserId) && ($valUserId == Yii::$app->user->id)) {
                            $reportMyInterest[] = $report;
                            continue;
                        }
                    }
                }

            }
            return $reportMyInterest;
        } else {
            return [];
        }
    }

    /**
     *
     */
    public function init()
    {
        $this->myActivitiesList = new MyActivitiesList();
        parent::init();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return AmosMyActivities::t('amosmyactivities', 'My activities');
    }

    /**
     * TODO Not yet terminated
     * @return array
     */
    private function getSurveyToValidate()
    {
        if (Yii::$app->hasModule('sondaggi')) {
            return []; // SurveyToValidate::find()->all();
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getShowcaseProjectToValidate()
    {
        if (Yii::$app->hasModule('showcaseprojects')) {
            $modelSearch = new ShowcaseProjectToValidate();
            $dataProvider = $modelSearch->searchToValidateShowcaseProjects(Yii::$app->request->getQueryParams());
            $ids = ArrayHelper::map($dataProvider->models, 'id', 'id');
            $modelList = ShowcaseProjectToValidate::find()->andWhere(['id' => $ids])->all();
            return $modelList;
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    private function getPartnershipProfilesToValidate()
    {
        if (Yii::$app->hasModule('partnershipprofiles')) {
            $modelSearch = new PartnershipProfileToValidate();
            $dataProvider = $modelSearch->searchToValidate(Yii::$app->request->getQueryParams());
            $ids = ArrayHelper::map($dataProvider->models, 'id', 'id');
            $modelList = PartnershipProfileToValidate::find()->andWhere(['id' => $ids])->all();
            return $modelList;
        } else {
            return [];
        }
    }

    /**
     * TODO Not yet terminated
     * @return array
     */
    private function getExpressionOfInterestToEvaluate()
    {
        if (Yii::$app->hasModule('partnershipprofiles')) {
            $modelSearch = new ExpressionOfInterestToEvaluate();
            $dataProvider = $modelSearch->searchForFacilitator(Yii::$app->request->getQueryParams());
            $ids = ArrayHelper::map($dataProvider->models, 'id', 'id');
            $modelList = ExpressionOfInterestToEvaluate::find()->andWhere(['id' => $ids])->all();
            return $modelList;
        } else {
            return [];
        }
    }
}