<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core
 * @category   CategoryName
 */

namespace lispa\amos\layout\assets;

use yii\web\AssetBundle;

class FormAsset extends AssetBundle
{
    public $css = [
        // TODO MOVE FROM BASE ASSET
        //'less/form.less',
    ];
    public $js = [
        'js/form.js',      
    ];
    public $depends = [
        //'lispa\amos\layout\assets\BaseAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/resources/form';

        parent::init();
    }
}
