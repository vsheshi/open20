<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\dashboard\controllers
 * @category   CategoryName
 */

namespace lispa\amos\dashboard\controllers;

use lispa\amos\core\helpers\BreadcrumbHelper;
use lispa\amos\dashboard\controllers\base\DashboardController;
use yii\helpers\Url;

/**
 * Class DefaultController
 * @package lispa\amos\dashboard\controllers
 */
class DefaultController extends DashboardController
{

    /**
     * @inheritdoc
     */
    public function init() {

        parent::init();
        $this->setUpLayout();
   
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh)) {
            //$moduleCwh->resetCwhScopeInSession();
        }
        $this->setUpLayout('dashboard');
        Url::remember();

        BreadcrumbHelper::reset();
        
        $params = [
            'currentDashboard' => $this->getCurrentDashboard()
        ];
        
        return $this->render('index', $params);
    }
     
     
}
