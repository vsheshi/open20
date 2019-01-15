<?php

/**
 * @package yii2-export
 * @version 1.2.9
 */

namespace kartik\export;

use kartik\base\AssetBundle;

/**
 * Asset bundle for ExportMenu Widget (for export menu data)
 *
 * @since 1.0
 */
class ExportMenuAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/kv-export-data']);
        $this->setupAssets('css', ['css/kv-export-data']);
        parent::init();
    }
}
