<?php
namespace lispa\amos\utility\controllers;


/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    piattaforma-openinnovation
 * @category   CategoryName
 */

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

class ConsoleController extends Controller
{
    
    
    public $layout = 'main';
    
    /**
     * Disable CSRF
     */
    public function init() {
        parent::init();
        $this->enableCsrfValidation = false;
        $this->setUpLayout();
    }
    
    
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
                            'run-cmd',
                        ],
                        'roles' => ['ADMIN']
                    ],
                ],
            ],
        ];
    }
    
    
    
    public function actionIndex()
    {
        return $this->render('index');
    }
             
    /**
     * 
     * @param string $cmd
     */
    public function actionRunCmd($cmd)
    {
        $output = '';
        Yii::$app->consoleRunner->run($cmd, $output);
        return $this->render('output',['output' => $output]);
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