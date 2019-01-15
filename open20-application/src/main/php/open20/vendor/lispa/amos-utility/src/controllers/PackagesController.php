<?php
namespace lispa\amos\utility\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;

class PackagesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                            'requirements'
                        ],
                        'roles' => ['ADMIN']
                    ],
                ],
            ],
        ];
    }

    public $layout = "main";

    /**
     * @inheritdoc
     */
    public function init() {

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
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

    public function actionIndex() {
        $basepath =  \Yii::$app->getBasePath().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;

        $composerLock = file_get_contents($basepath . 'composer.lock');
        $composerJson = file_get_contents($basepath . 'composer.json');

        return $this->render('index', [
            'composerLock' => $composerLock,
            'composerJson' => $composerJson
        ]);
    }

    public function actionRequirements() {
        require(__DIR__ . '/../../../../yiisoft/yii2/requirements/YiiRequirementChecker.php');

        $requirementsChecker = new \YiiRequirementChecker();
        return $requirementsChecker->checkYii()->render();
    }
}