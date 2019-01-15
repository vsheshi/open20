<?php

namespace lispa\amos\utility;

use Yii;
use lispa\amos\core\module\AmosModule;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * /**
 * due module definition class
 */
class Module extends AmosModule
{
    public $controllerNamespace = 'lispa\amos\utility\controllers';

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        //Let console working right
        $this->setControllerNameSpace(\Yii::$app);

        //Configuration
        $config = require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

        //Setup config
        \Yii::configure($this, ArrayHelper::merge($config, $this));
        $tasksModule = $this->getModule('manage-tasks');
        $layoutModule = \Yii::$app->getModule('layout');

        if ($tasksModule && $layoutModule) {
            \Yii::$app->setLayoutPath($layoutModule->layoutPath);
        }
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

    /**
     * @return string
     */
    public static function getModuleName()
    {
        return 'utility';
    }

    /**
     * Get default models
     * @return array
     */
    protected function getDefaultModels()
    {
        return [
        ];
    }

    /**
     * @param $app
     */
    public function bootstrap($app)
    {
        $this->setControllerNameSpace($app);
    }


    /**
     * @param \yii\console\Application $app
     */
    private function setControllerNameSpace($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'lispa\amos\utility\commands';
        }
    }
}