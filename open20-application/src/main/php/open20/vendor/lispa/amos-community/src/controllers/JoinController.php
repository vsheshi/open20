<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\controllers;


use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\community\rbac\UpdateOwnNetworkCommunity;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class JoinController extends Controller
{
    /**
     * @var string $layout
     */
//    public $layout = 'main';
    public $layout = 'room';

    /**
     * @inheritdoc
     */
    public function init() {

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
    }

    /**
     * @inheritdoc
     * @return mixed
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
                            'index',
                        ],
                        'roles' => [UpdateOwnNetworkCommunity::className()]
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'remove'
                        ],
                        'roles' => ['COMMUNITY_MEMBER', 'COMMUNITY_READER', 'AMMINISTRATORE_COMMUNITY', 'BASIC_USER']
                    ],
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

    public function actionIndex($id)
    {

        $model = $this->findModel($id);

        $userCommunity = CommunityUserMm::findOne(['user_id' => Yii::$app->user->id, 'community_id' => $id]);

        /**
         * If The User is not subscribed to community
         */
        if(empty($userCommunity)) {
            Yii::$app->session->addFlash('danger', AmosCommunity::t('amosadmin', 'You Can\'t access a community you are not a member of'));

            return $this->redirect(Url::previous());
        }

        if ($model != null) {
            $moduleCwh = \Yii::$app->getModule('cwh');
            if (isset($moduleCwh)) {
                $moduleCwh->setCwhScopeInSession([
                    'community' => $id,
                ],
                [
                    'mm_name' => 'community_user_mm',
                    'entity_id_field' => 'community_id',
                    'entity_id' => $id
                ]);
            }
        }

        $this->setListsBreadcrumbs($model);
        return $this->render('index', [
            'model' => $model
        ]);

    }

    /**
     * @return string
     */
    public function actionRemove()
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh)) {
            $moduleCwh->resetCwhScopeInSession();
        }
        return;
    }

    /**
     * Used to set page title and breadcrumbs.
     *
     * @param Community $model Page title (ie. Created by, ...)
     */
    private function setListsBreadcrumbs($model)
    {
        if($model->context != Community::className()){
            $contextModel = Yii::createObject($model->context);
            $callingModel = $contextModel::findOne(['community_id' => $model->id]);
//            $createRedirectUrlParams = [
//                $callingModel->getPluginModule() . '/' . $callingModel->getPluginController() . '/' . $callingModel->getRedirectAction(),
//                'id' => $callingModel->id,
//            ];
//            $redirectUrl = Yii::$app->urlManager->createUrl($createRedirectUrlParams);
            Yii::$app->view->params['breadcrumbs'][] = [
                'label' => $model->name,
                'url' => Url::previous(),
                'remove_action' => '/community/join/remove'
            ];
        }else {
            Yii::$app->view->params['breadcrumbs'][] = [
                'label' => AmosCommunity::t('amoscommunity', 'Community'),
                'url' => \yii\helpers\Url::to('/community'),
                'remove_action' => '/community/join/remove'
            ];
        }
        Yii::$app->view->params['breadcrumbs'][] = AmosCommunity::t('amoscommunity', "Dashboard" ). ' ' . $model->name;
    }

    /**
     * Finds the Community model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Community the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Community::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(AmosCommunity::t('amoscommunity', 'Requested page is not available.'));
        }
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
}