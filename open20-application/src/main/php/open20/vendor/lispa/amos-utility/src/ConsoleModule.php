<?php
namespace lispa\amos\utility;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * /**
 * due module definition class
 */
class ConsoleModule extends \yii\base\Module
{
    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        //Configuration
        $config = require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'console_config.php');

        //Setup config
        \Yii::configure($this, ArrayHelper::merge($config, $this));

        $this->defaultRoute = 'console';
    }
}