<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\uploader\controllers
 * @category   CategoryName
 */

namespace lispa\amos\uploader\controllers;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller as YiiController;

/**
 * Class UploadController
 * @package lispa\amos\uploader\controllers
 */
class UploadController extends YiiController
{

    public $layout = 'form';

    /**
     * @inheritdoc
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
                        'roles' => ['UPLOADER_ADMINISTRATOR']
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

    /**
     * @inheritdoc
     */
    public function init() {

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
    }

    public function actionIndex()
    {
        $isEdge = false;
        if(preg_match("/Edge/i", $_SERVER['HTTP_USER_AGENT'], $output_array)){
            $isEdge = true;
        }
//        if (preg_match('/MSIE\s(?P<v>\d+)/i', $_SERVER['HTTP_USER_AGENT'], $B) && $B['v'] <= 9){
//            return $this->render('index_ie9');
//        }
//        else {
            return $this->render('index', ['isEdge' => $isEdge]);
//        }

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