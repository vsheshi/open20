<?php
/**
 */

namespace lispa\amos\groups\assets;

use yii\web\AssetBundle;


class SpinnerWaitAsset extends AssetBundle
{
    public $sourcePath = '@vendor/lispa/amos-groups/src/assets/web';

    public $css = [
        'css/spinner.css'
    ];
    public $js = [
    ];
    public $depends = [
    ];
}
