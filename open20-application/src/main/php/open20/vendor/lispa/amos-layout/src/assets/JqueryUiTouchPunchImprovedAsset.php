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

class JqueryUiTouchPunchImprovedAsset extends AssetBundle {

    public $sourcePath = '@bower/jquery-ui-touch-punch-improved';
    public $js = [
        'jquery.ui.touch-punch-improved.js',
    ];
    public $depends = [
        'yii\jui\JuiAsset'
    ];

}
