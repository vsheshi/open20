<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\dashboard
 * @category   CategoryName
 */

namespace lispa\amos\dashboard;

use lispa\amos\core\module\AmosModule;
use yii\base\BootstrapInterface;

class AmosDashboard extends AmosModule implements BootstrapInterface
{
    public static $CONFIG_FOLDER       = 'config';
    public $controllerNamespace        = 'lispa\amos\dashboard\controllers';
    public $controllerConsoleNamespace = 'lispa\amos\dashboard\commands';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';
    public $name   = 'Dashboard';

    //public $initWidgets = true;

    /**
     * If true the widgets will be refreshed if
     * the AmosWidgets.created_at > AmosUserDashboars.updated_at
     * @var boolean
     */
    public $refreshWidgets       = true;
    public $initIfEmpty          = true;
    public $initAllWidgets       = false;
    public $initHierarchyWidgets = true;
    public $initChildWidget      = false;

    /**
     * Array of the modules that have the sub-dashboard
     * @var array
     */
    public $modulesSubdashboard;

    /**
     * If true, only widgets that have dashboard_visible set to 1 will be shown
     * @var boolean $useWidgetGraphicDashboardVisible
     */
    public $useWidgetGraphicDashboardVisible = false;

    /**
     *
     * @var type $useWidgetGraphicOrder
     */
    public $useWidgetGraphicOrder = false;

    /**
     * @return string
     */
    public static function getModuleName()
    {
        return "dashboard";
    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = $this->controllerConsoleNamespace;
        }
    }

    public function init()
    {
        parent::init();
        \Yii::setAlias('@lispa/amos/'.static::getModuleName().'/controllers', __DIR__.'/controllers/');
        // initialize the module with the configuration loaded from config.php
        //  \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . self::$CONFIG_FOLDER . DIRECTORY_SEPARATOR . 'config.php'));
    }

    public function getWidgetIcons()
    {

    }

    public function getWidgetGraphics()
    {

    }

    protected function getDefaultModels()
    {
        // TODO: Implement getDefaultModels() method.
    }

    public function setModuleSubDashboard($widgets)
    {
        if (is_array($widgets)) {
            
        } else if (is_string($widgets)) {
            $widgets = [$widgets];
        }
    }
}