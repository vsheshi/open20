<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\upload
 * @category   CategoryName
 */

namespace lispa\amos\upload;

use pendalf89\filemanager\Module;

class AmosUpload extends Module
{
    public $controllerNamespace = 'lispa\amos\upload\controllers';

    public function init()
    {
        parent::init(
        // initialize the module with the configuration loaded from config.php
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php')));
    }
}
