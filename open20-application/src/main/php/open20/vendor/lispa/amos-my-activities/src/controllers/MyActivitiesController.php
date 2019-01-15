<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\controllers
 * @category   CategoryName
 */

namespace lispa\amos\myactivities\controllers;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\comuni\models\Query;
use lispa\amos\core\controllers\CrudController;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\myactivities\AmosMyActivities;
use lispa\amos\myactivities\models\MyActivities;
use lispa\amos\myactivities\models\search\MyActivitiesModelSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * MyActivitiesController implements the CRUD actions
 */
class MyActivitiesController extends CrudController
{
    /**
     * @var string $layout
     */
    public $layout = 'list';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setModelObj(new MyActivities());
        $this->setModelSearch(new MyActivities());

        $this->setAvailableViews([

            'list' => [
                'name' => 'list',
                'label' => AmosMyActivities::t('amosmyactivities', '{listIcon}' . Html::tag('p', 'List'), [
                    'listIcon' => AmosIcons::show('view-list')
                ]),
                'url' => '?currentView=list'
            ],

        ]);

        parent::init();
        $this->setUpLayout();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                        ],
                        'roles' => ['ADMIN', 'VALIDATED_BASIC_USER']
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
    }

    /**
     * @param null $layout
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex($layout = NULL)
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh)) {
            $moduleCwh->resetCwhScopeInSession();
        }
        Url::remember();
        $this->setUpLayout('list');

        $modelSearch = new MyActivitiesModelSearch;
        $modelSearch->load(\Yii::$app->request->getQueryParams());

        $model = new MyActivities;
        $listOfActivities = $model->getMyActivities($modelSearch);
        if (count($listOfActivities) > 0) {
            $dataProvider = new ActiveDataProvider();
            $dataProvider->setModels($listOfActivities);
            $this->setDataProvider($dataProvider);
            $this->parametro['empty'] = false;
        } else {
            $this->parametro['empty'] = true;
        }

        $this->setModelSearch($modelSearch);
        return parent::actionIndex();
    }

    /**
     * @param null $layout
     * @return bool
     */
    public function setUpLayout($layout = null){
        if ($layout === false){
            $this->layout = false;
            return true;
        }
        $module = \Yii::$app->getModule('layout');
        if(empty($module)){
            $this->layout =  '@vendor/lispa/amos-core/views/layouts/' . (!empty($layout) ? $layout : $this->layout);
            return true;
        }
        $this->layout = (!empty($layout)) ? $layout : $this->layout;
        return true;
    }
}