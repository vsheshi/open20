<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */

namespace lispa\amos\emailmanager\controllers;

use Yii;
use yii\helpers\Url;
use lispa\amos\dashboard\controllers\base\DashboardController;


class DefaultController extends DashboardController
{   
    public $modelName;
    public $layout = 'dashboard_interna';

    /**
     * @inheritdoc
     */
    public function init() {

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
    }

    /**
     *
     */
    public function actionIndex($layout = null)
    {
        Url::remember();
        
        return $this->render('index');
    }

}