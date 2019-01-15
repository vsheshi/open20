<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    pcd20\import
 * @category   CategoryName
 */

namespace pcd20\import;

use lispa\amos\core\module\AmosModule;
use yii\helpers\ArrayHelper;

/**
 * Class Module
 * @package pcd20\import
 */
class Module extends AmosModule
{
    public static $CONFIG_FOLDER = 'config';

    /**
     * @inheritdoc
     */
    static $name = 'import';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    //public $layout = 'main';

    public $layout = '@vendor/lispa/amos-core/views/layouts/main';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'pcd20\import\controllers';

    public $timeout = 180;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        //Configuration
        $config = require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        \Yii::configure($this, ArrayHelper::merge($config, $this));
    }

    public static function getModuleName()
    {
        return self::$name;
    }

    /**
     * @inheritdoc
     */
    public function getAmosUniqueId()
    {
        return 'pcd20import';
    }

    public function getWidgetIcons()
    {
        return [
        ];
    }

    public function getWidgetGraphics()
    {
        return [
        ];
    }

    protected function getDefaultModels()
    {
        return [
        ];
    }
}
