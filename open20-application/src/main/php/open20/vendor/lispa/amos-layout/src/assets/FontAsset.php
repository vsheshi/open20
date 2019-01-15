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
class FontAsset extends AssetBundle
{
    public $js = [];

    public $css = [
        'style-fonts.css'
    ];

    public $depends = [
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/resources/fonts';

        parent::init();
    }
}

