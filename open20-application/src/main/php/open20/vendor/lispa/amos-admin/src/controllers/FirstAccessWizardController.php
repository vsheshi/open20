<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\controllers
 * @category   CategoryName
 */

namespace lispa\amos\admin\controllers;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\assets\ModuleAdminAsset;
use lispa\amos\admin\components\FirstAccessWizardParts;
use lispa\amos\admin\interfaces\OrganizationsModuleInterface;
use lispa\amos\admin\models\search\UserProfileAreaSearch;
use lispa\amos\admin\models\search\UserProfileRoleSearch;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\controllers\CrudController;
use lispa\amos\core\forms\editors\m2mWidget\controllers\M2MWidgetControllerTrait;
use lispa\amos\core\forms\editors\m2mWidget\M2MEventsEnum;
use lispa\amos\core\utilities\ArrayUtility;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class FirstAccessWizardController
 *
 * @property \lispa\amos\admin\models\UserProfile $model
 *
 * @package lispa\amos\admin\controllers
 */
class FirstAccessWizardController extends CrudController
{
    /**
     * @var string $layout
     */
    public $layout = 'list';

    /**
     * M2MWidgetControllerTrait
     */
    use M2MWidgetControllerTrait;

    /**
     * Working user ID
     */
    protected $userProfileId;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setModelObj(AmosAdmin::instance()->createModel('UserProfile'));
        $this->setModelSearch(AmosAdmin::instance()->createModel('UserProfileSearch'));
        $this->setAvailableViews([]);
        
        parent::init();
        $this->setUpLayout();

        ModuleAdminAsset::register(Yii::$app->view);

        $this->setUpLayout('progress_wizard');
        $this->setTitleAndBreadcrumbs(AmosAdmin::t('amosadmin', 'My Profile'));
        $this->setStartObjClassName(UserProfile::className());
        $this->setTargetObjClassName(UserProfile::className());
        
        $this->on(M2MEventsEnum::EVENT_BEFORE_ASSOCIATE_ONE2MANY, [$this, 'beforeAssociateOneToMany']);
        $this->on(M2MEventsEnum::EVENT_BEFORE_RENDER_ASSOCIATE_ONE2MANY, [$this, 'beforeRenderOneToMany']);
        $this->on(M2MEventsEnum::EVENT_BEFORE_CANCEL_ASSOCIATE_M2M, [$this, 'beforeCancelAssociateM2m']);
        $this->on(M2MEventsEnum::EVENT_AFTER_ASSOCIATE_ONE2MANY, [$this, 'afterAssociateOneToMany']);


        //Set current user id
        $this->userProfileId = Yii::$app->getUser()->identity->profile->id;
    }
    
    /**
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'introduction',
                            'introducing-myself',
                            'role-and-area',
                            'interests',
                            'partnership',
                            'finish',
                            'annulla-m2m',
                            'associate-facilitator',
                            'associate-prevalent-partnership'
                        ],
                        'roles' => ['@']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get']
                ]
            ]
        ]);
        return $behaviors;
    }
    
    /**
     * Used for set page title and breadcrumbs.
     * @param string $pageTitle
     */
    public function setTitleAndBreadcrumbs($pageTitle)
    {
        Yii::$app->view->title = $pageTitle;
        Yii::$app->view->params['breadcrumbs'] = [
            ['label' => $pageTitle]
        ];
    }
    
    /**
     * Set view params for the event creation wizard.
     */
    private function setParamsForView()
    {
        $parts = new FirstAccessWizardParts(['model' => $this->model]);
        Yii::$app->view->title = $parts->active['index'] . '. ' . $parts->active['label'];
        Yii::$app->view->params['model'] = $this->model;
        Yii::$app->view->params['partsQuestionario'] = $parts;
        Yii::$app->view->params['hidePartsLabel'] = true; // This param hide the second title under the wizard progress bar.
        Yii::$app->view->params['disablePlatformLinks'] = true;
        Yii::$app->view->params['hideBreadcrumb'] = true; // This param hide the breadcrumb in the wizard layout.
        Yii::$app->view->params['hidePartsUrl'] = true; // This param disable the progress wizard menu links.
    }
    
    /**
     * 
     * @return type
     */
    public function goToNextPart()
    {
        $parts = new FirstAccessWizardParts(['model' => $this->model]);
        return $this->redirect([$parts->getNext()]);
    }
    
    /**
     * @param \yii\base\Event $event
     */
    public function beforeAssociateOneToMany($event)
    {
        $this->setUpLayout('main');
    }
    
    /**
     * @param \yii\base\Event $event
     */
    public function beforeRenderOneToMany($event)
    {
        $this->setParamsForView();
    }

    /**
     * @param $event
     */
    public function afterAssociateOneToMany($event)
    {

        try {
            $userprofile_class = AmosAdmin::getInstance()->model('UserProfile');

            if (!empty($event->sender) && is_object($event->sender) && $event->sender instanceof $userprofile_class) {
                if (!empty($event->sender->prevalent_partnership_id)) {
                    $admin = AmosAdmin::getInstance();
                    /** @var  $organizationsModule OrganizationsModuleInterface */
                    $organizationsModule = \Yii::$app->getModule($admin->getOrganizationModuleName());
                    $organizationsModule->saveOrganizationUserMm(Yii::$app->user->id, $event->sender->prevalent_partnership_id);
                }
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }
    
    /**
     * @param $event
     */
    public function beforeCancelAssociateM2m($event)
    {
        $get = Yii::$app->getRequest()->get();
        if (isset($get['action'])) {
            switch ($get['action']) {
                case 'associate-facilitator':
                    $this->setRedirectAction('introducing-myself');
                    break;
                case 'associate-prevalent-partnership':
                    $this->setRedirectAction('partnership');
                    break;
            }
        }
    }
    
    /**
     * 
     * @return type
     */
    public function actionAssociateFacilitator()
    {
        $this->setMmTargetKey('facilitatore_id');
        $this->setRedirectAction('introducing-myself');
        $this->setTargetUrl('associate-facilitator');
        return $this->actionAssociateOneToMany($this->userProfileId);
    }
    
    /**
     * 
     * @return type
     */
    public function actionAssociatePrevalentPartnership()
    {
        $this->setMmTargetKey('prevalent_partnership_id');
        $this->setRedirectAction('partnership');
        $this->setTargetUrl('associate-prevalent-partnership');
        return $this->actionAssociateOneToMany($this->userProfileId);
    }
    
    /**
     * @return string|\yii\web\Response
     */
    public function actionIntroduction()
    {

        Url::remember();
        
        $this->model = $this->findModel($this->userProfileId);
        $this->model->setScenario(UserProfile::SCENARIO_INTRODUCTION);
        if (Yii::$app->getRequest()->post()) {
            return $this->goToNextPart();
        }

        // If the user has never accessed to the first access wizard, this will create a new array (jsonified)
        // that will be saved in the db and saves the steps opened once at least
        if($this->model->first_access_wizard_steps_accessed==""){

            $parts = [];

            foreach ((new FirstAccessWizardParts(['model' => $this->model]))::$map as $partName => $partValue){
                $parts[$partName] = false;
            }

            $this->model->first_access_wizard_steps_accessed = Json::encode($parts);
            $this->model->save(false);

        }

        $this->setAccessFirstTime(FirstAccessWizardParts::PART_INTRODUCTION);

        $this->setParamsForView();
        return $this->render('introduction', [
            'model' => $this->model
        ]);
    }
    
    /**
     * @return string|\yii\web\Response
     */
    public function actionIntroducingMyself()
    {
        Url::remember();
        
        $this->model = $this->findModel($this->userProfileId);
        
        // Set default facilitator if an other facilitator is not present.
        if (!$this->model->facilitatore_id && !is_null($this->model->getDefaultFacilitator())) {
            $this->model->facilitatore_id = $this->model->getDefaultFacilitator()->id;
            $this->model->save(false);
        }
        
        $this->model->setScenario(UserProfile::SCENARIO_INTRODUCING_MYSELF);
        if (Yii::$app->getRequest()->post() && $this->model->load(Yii::$app->getRequest()->post()) && $this->model->save()) {
            return $this->goToNextPart();
        }

        $this->setAccessFirstTime(FirstAccessWizardParts::PART_INTRODUCING_MYSELF);
        
        $this->setParamsForView();
        return $this->render('introducing_myself', [
            'model' => $this->model,
            'facilitatorUserProfile' => $this->model->facilitatore
        ]);
    }
    
    /**
     * @return string|\yii\web\Response
     */
    public function actionRoleAndArea()
    {
        Url::remember();
        
        $this->model = $this->findModel($this->userProfileId);
        
        if (Yii::$app->getRequest()->post()) {
            $this->model->setScenario(UserProfile::SCENARIO_ROLE_AND_AREA);
            if ($this->model->load(Yii::$app->getRequest()->post()) && $this->model->save()) {
                return $this->goToNextPart();
            }
        }

        $this->setAccessFirstTime(FirstAccessWizardParts::PART_ROLE_AND_AREA);
        
        $this->setParamsForView();
        $this->model->setScenario(UserProfile::SCENARIO_ROLE_AND_AREA);
        return $this->render('role_and_area', [
            'model' => $this->model
        ]);
    }
    
    /**
     * @return string|\yii\web\Response
     */
    public function actionInterests()
    {
        Url::remember();
        
        $this->model = $this->findModel($this->userProfileId);
        
        if (Yii::$app->getRequest()->post()) {
            $this->model->setScenario(UserProfile::SCENARIO_INTERESTS);
            if ($this->model->load(Yii::$app->getRequest()->post()) && $this->model->save()) {
                return $this->goToNextPart();
            }
        }

        $this->setAccessFirstTime(FirstAccessWizardParts::PART_INTERESTS);
        
        $this->setParamsForView();
        $this->model->setScenario(UserProfile::SCENARIO_INTERESTS);
        return $this->render('interests', [
            'model' => $this->model
        ]);
    }
    
    /**
     * @return string|\yii\web\Response
     */
    public function actionPartnership()
    {
        Url::remember();
        
        $this->model = $this->findModel($this->userProfileId);
        
        if (Yii::$app->getRequest()->post()) {
            $this->model->setScenario(UserProfile::SCENARIO_PARTNERSHIP);
            $this->model->status = UserProfile::USERPROFILE_WORKFLOW_STATUS_TOVALIDATE;
            if ($this->model->load(Yii::$app->getRequest()->post()) && $this->model->save()) {
                /** @var AmosAdmin $adminModule */
                $adminModule = Yii::$app->getModule(AmosAdmin::getModuleName());
                if ($adminModule->bypassWorkflow) {
                    $this->model->status = UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED;
                    $ok = $this->model->save();
                    if ($ok) {
                        return $this->goToNextPart();
                    }
                } else {
                    return $this->goToNextPart();
                }
            }
        }

        $this->setAccessFirstTime(FirstAccessWizardParts::PART_PARTNERSHIP);
        
        $this->setParamsForView();
        $this->model->setScenario(UserProfile::SCENARIO_PARTNERSHIP);
        return $this->render('partnership', [
            'model' => $this->model
        ]);
    }

//    /**
//     * Action for summary step of the wizard.
//     * @param int $id The user profile id.
//     * @return string
//     */
//    public function actionSummary($id)
//    {
//        Url::remember();
//
//        $this->model = $this->findModel($id);
//        $this->model->setScenario(UserProfile::SCENARIO_SUMMARY);
////        $managerStatus = CommunityUserMm::STATUS_MANAGER_TO_CONFIRM;
////
////        if (Yii::$app->getRequest()->post() && $this->model->load(Yii::$app->getRequest()->post())) {
////            if ($this->model->status == Event::EVENTS_WORKFLOW_STATUS_PUBLISHED) {
////                $managerStatus = CommunityUserMm::STATUS_ACTIVE;
////                $this->model->validated_at_least_once = Event::BOOLEAN_FIELDS_VALUE_YES;
////                $this->model->visible_in_the_calendar = Event::BOOLEAN_FIELDS_VALUE_YES;
////            } else if (($this->model->status == Event::EVENTS_WORKFLOW_STATUS_PUBLISHREQUEST) && (in_array($this->model->regola_pubblicazione, [3, 4]))) {
////                $managerStatus = CommunityUserMm::STATUS_ACTIVE;
////            }
////
////            $communityToCreate = false;
////            if (!$this->model->community_id && $this->model->event_management && ($this->model->status != Event::EVENTS_WORKFLOW_STATUS_DRAFT)) {
////                EventsUtility::createCommunity($this->model, $managerStatus);
////                $communityToCreate = true;
////            }
////            $this->model->detachBehaviorByClassName(CwhNetworkBehaviors::className());
////            if ($this->model->save()) {
////                $ok = true;
////                if ($communityToCreate && $this->model->community_id) {
////                    $ok = EventsUtility::duplicateEventLogoForCommunity($this->model);
////                }
////                if ($ok) {
////                    return $this->goToNextPart();
////                } else {
////                    Yii::$app->getSession()->addFlash('danger', AmosEvents::t('amosevents', 'There was an error while saving.'));
////                }
////            }
////        }
////
////        $viewPublish = 'hidden';
////        $viewPublishRequest = '';
////        $loggedUser = Yii::$app->getUser();
////        $canDirectlyPublishRoles = [
////            'EVENTS_ADMINISTRATOR',
////            'EVENTS_VALIDATOR',
////            'EVENTS_VALIDATOR_PLATFORM'
////        ];
////        foreach ($canDirectlyPublishRoles as $canPublishRole) {
////            if ($loggedUser->can($canPublishRole)) {
////                $viewPublish = '';
////                $viewPublishRequest = 'hidden';
////            }
////        }
////
//        $this->setParamsForView();
//        return $this->render('summary', [
//            'model' => $this->model,
////            'viewPublish' => $viewPublish,
////            'viewPublishRequest' => $viewPublishRequest
//        ]);
//    }
    
    /**
     * @param int $id The user profile id.
     * @return string|\yii\web\Response
     */
    public function actionFinish()
    {
        Url::remember();
        $this->model = $this->findModel($this->userProfileId);

        $this->setAccessFirstTime(FirstAccessWizardParts::PART_FINISH);

        $this->setParamsForView();
        return $this->render('finish', [
            'model' => $this->model
        ]);
    }
    
    /**
     * This method return all enabled professional roles translated.
     * @return array
     */
    public function getRoles()
    {
        $roles = ArrayUtility::translateArrayValues(
            ArrayHelper::map(UserProfileRoleSearch::find()->andWhere('name!="Other"')->asArray()->all(), 'id', 'name'),
            'amosadmin',
            AmosAdmin::className()
        );
        asort($roles);
        $other = ArrayUtility::translateArrayValues(
            ArrayHelper::map(UserProfileRoleSearch::find()->andWhere('name="Other"')->asArray()->all(), 'id', 'name'),
            'amosadmin',
            AmosAdmin::className()
        );
        return $roles+$other;
    }
    
    /**
     * This method return all enabled professional areas translated.
     * @return array
     */
    public function getAreas()
    {
        $areas = ArrayUtility::translateArrayValues(
            ArrayHelper::map(UserProfileAreaSearch::find()->andWhere('name!="Other"')->asArray()->all(), 'id', 'name'),
            'amosadmin',
            AmosAdmin::className()
        );
        asort($areas);
        $other = ArrayUtility::translateArrayValues(
            ArrayHelper::map(UserProfileAreaSearch::find()->andWhere('name="Other"')->asArray()->all(), 'id', 'name'),
            'amosadmin',
            AmosAdmin::className()
        );
        return $areas+$other;
    }

    /**
     * @param null $layout
     * @return bool
     */
    public function setUpLayout($layout = null)
    {
        if ($layout === false) {
            $this->layout = false;
            return true;
        }
        $this->layout = (!empty($layout)) ? $layout : $this->layout;
        $module = \Yii::$app->getModule('layout');
        if (empty($module)) {
            if (strpos($this->layout, '@') === false) {
                $this->layout = '@vendor/lispa/amos-core/views/layouts/'.(!empty($layout) ? $layout : $this->layout);
            }
            return true;
        }
        return true;
    }

    /**
     * Sets in the user_profile table an accessed step for the first time
     * @param string $step
     */
    public function setAccessFirstTime($step){

        if($this->model->first_access_wizard_steps_accessed!=null && $this->model->first_access_wizard_steps_accessed!="") {

            $stepsAccessed = Json::decode($this->model->first_access_wizard_steps_accessed);

            if (!$stepsAccessed[$step]) {

                $stepsAccessed[$step] = true;

                $this->model->first_access_wizard_steps_accessed = Json::encode($stepsAccessed);
                $this->model->save(false);

            }

        }


    }

}