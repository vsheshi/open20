<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-select2
 * @version 2.0.8
 */

namespace kartik\select2;

use kartik\base\AssetBundle;

/**
 * Krajee Select2 theme
 *
 * @since 1.0
 */
class ThemeKrajeeAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/select2-krajee']);
        parent::init();
    }
}
