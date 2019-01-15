<?php

namespace lispa\amos\gantt\assets;

use yii\web\AssetBundle;

/**
 * Class AmosGanttAsset
 *
 * @package lispa\amos\gantt\assets
 *
 */
class AmosGanttAsset extends AssetBundle
{

    /**
     * @var string
     */
    public $sourcePath = '@vendor/lispa/amos-gantt/src/assets/amos-gantt-asset/web';

    /**
     * @var array
     */
    public $css = [
        'css/gantt.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'js/gantt.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        'lispa\amos\gantt\assets\GanttAsset',
    ];
}