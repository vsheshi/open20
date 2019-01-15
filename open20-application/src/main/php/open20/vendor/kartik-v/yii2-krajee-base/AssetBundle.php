<?php

/**
 * @package   yii2-krajee-base
 * @version   1.8.9
 */

namespace kartik\base;

/**
 * Asset bundle used for all Krajee extensions with bootstrap and jquery dependency.
 *
 * @since 1.0
 */
class AssetBundle extends BaseAssetBundle
{
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
