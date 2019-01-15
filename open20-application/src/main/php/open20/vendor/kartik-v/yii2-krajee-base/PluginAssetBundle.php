<?php

/**
 * @package   yii2-krajee-base
 * @version   1.8.7
 */

namespace kartik\base;

/**
 * Base asset bundle for Krajee extensions (including bootstrap plugins)
 *
 * @since 1.6.0
 */
class PluginAssetBundle extends AssetBundle
{
    /**
     * @inheritdoc
     */
     public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
