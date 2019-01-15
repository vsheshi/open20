<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\workflow
 * @category   CategoryName
 */

namespace lispa\amos\workflow;

use lispa\amos\core\module\Module;
use lispa\amos\core\module\ModuleInterface;
use lispa\amos\core\record\Record;
use lispa\amos\workflow\components\events\SimpleWorkFlowEventsListener;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;
use yii\base\Event;

/**
 * Class AmosWorkflow
 * @package lispa\amos\workflow
 */
class AmosWorkflow extends Module implements ModuleInterface
{
    public static $CONFIG_FOLDER = 'config';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    public $name = 'Workflow';

    public static function getModuleName()
    {
        return "workflow";
    }

    public function init()
    {
        parent::init();
        // initialize the module with the configuration loaded from config.php
        Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . self::$CONFIG_FOLDER . DIRECTORY_SEPARATOR . 'config.php'));

        Event::on(Record::className(), SimpleWorkflowBehavior::EVENT_AFTER_CHANGE_STATUS, [SimpleWorkFlowEventsListener::className(), 'afterChangeStatus']);
    }

    public function getWidgetIcons()
    {
        return [];
    }

    public function getWidgetGraphics()
    {
        return [];
    }
}