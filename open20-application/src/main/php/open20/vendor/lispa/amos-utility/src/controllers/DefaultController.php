<?php
namespace lispa\amos\utility\controllers;

use lispa\amos\core\module\ModuleInterface;
use lispa\amos\utility\Module;
use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;
use lispa\amos\core\utilities\ClassUtility;

class DefaultController extends Controller
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
                            'reset-dashboard-by-module'
                        ],
                        'roles' => ['ADMIN']
                    ],
                ],
            ],
        ];
    }

    public  $layout = "main";

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

    public function actionIndex()
    {
        return $this->render('index');
    }


    /**
     *
     */
    public /**void*/function actionResetDashboardByModule(/**void*/){
        $classname = 'lispa\amos\dashboard\utility\DashboardUtility';

        try {
            if (ClassUtility::classExist($classname)) {
                $modules = \Yii::$app->getModules();
                if(!empty($modules)) {
                    foreach ($modules as $id => $module) {
                        $moduleObj = Yii::$app->getModule($id);
                        $class = new \ReflectionClass($moduleObj);
                        if ($class->implementsInterface(ModuleInterface::class)) {
                            $classname::resetDashboardsByModule($moduleObj->getModuleName());
                        }
                    }
                    Yii::$app->getSession()->addFlash('success', Module::t('amosutility', 'Dashboard resetted'));
                }
            }
        }catch (\yii\base\Exception $ex){
            Yii::$app->getSession()->addFlash('error', Module::t('amosutility', 'Dashboard not resetted'));
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        return $this->redirect('index');
    }
}