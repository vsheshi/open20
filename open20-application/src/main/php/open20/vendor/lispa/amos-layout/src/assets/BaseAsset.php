<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\layout
 * @category   CategoryName
 */

namespace lispa\amos\layout\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset
 * @package lispa\amos\layout\assets
 */
class BaseAsset extends AssetBundle
{
    public $js = [
        'js/bootstrap-tabdrop.js',
        'js/globals.js',
    ];

    public $css = [
        'less/main.less'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapAsset',
        'kartik\select2\Select2Asset',
        'lispa\amos\layout\assets\IEAssets',
        'lispa\amos\layout\assets\JqueryUiTouchPunchImprovedAsset',
        'lispa\amos\layout\assets\ConflictJuiBootstrap',
        'lispa\amos\layout\assets\TourAsset',
        'lispa\amos\layout\assets\IconAsset',
        'lispa\amos\layout\assets\FontAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/resources/base';

        parent::init();
    }
}
