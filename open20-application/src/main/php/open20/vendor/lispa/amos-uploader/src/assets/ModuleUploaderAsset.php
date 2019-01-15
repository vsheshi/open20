<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\uploader
 * @category   CategoryName
 */

namespace lispa\amos\uploader\assets;

use yii\web\AssetBundle;

class ModuleUploaderAsset extends AssetBundle
{
    public $sourcePath = '@vendor/lispa/amos-uploader/src/assets/web';

    public $css = [
        'css/uploader.css'
    ];
    public $js = [
        'js/uploader.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];
}
