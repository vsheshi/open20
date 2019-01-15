<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\dashboard
 * @category   CategoryName
 */

namespace lispa\amos\dashboard\widgets;

use Yii;
use yii\base\Widget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\dashboard\AmosDashboard;
use lispa\amos\dashboard\models\search\AmosWidgetsSearch;
use lispa\amos\dashboard\models\AmosWidgets;
use lispa\amos\dashboard\assets\SubDashboardAsset;
use lispa\amos\core\views\assets\AmosCoreAsset;
use lispa\amos\dashboard\assets\ModuleDashboardAsset;

/**
 * Class DashboardWidget
 * @package lispa\amos\dashboard\widgets
 */
class DashboardWidget extends Widget
{
    /**
     * Title that show in the breadcrumb
     * @var string
     */
    public $title;
    public $forceAll          = true;
    public $classDivGraphic   = 'grid-item';
    public $graphicCustomSize = false;

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();
        if (empty($this->title)) {
            $this->title = AmosDashboard::t('amosdashboard', 'Dashboard del plugin');
        }
    }

    /**
     * @return mixed
     */
    public function run()
    {
        return $this->getHtml();
    }

    /**
     * This method render the widget
     * @param type $icons
     * @param type $graphics
     * @return type
     */
    protected function getHtml()
    {
        $moduleL            = \Yii::$app->getModule('layout');
        $layoutModuleSet    = isset($moduleL);
        $showWidgetsGraphic = [];
        $controller         = \Yii::$app->controller;
        $currentDashboard   = $controller->getCurrentDashboard();

        AmosCoreAsset::register($controller->getView());
        ModuleDashboardAsset::register($controller->getView());
        AmosIcons::map($controller->getView());
        SubDashboardAsset::register($controller->getView());

        return $this->render('dashboard',
                [
                'layoutModuleSet' => $layoutModuleSet,
                'currentDashboard' => $currentDashboard,
                'classDivGraphic' => $this->classDivGraphic,
                'graphicCustomSize' => $this->graphicCustomSize,
                'forceAll' => $this->forceAll,
                'title' => $this->title,
                ]
        );
    }
}