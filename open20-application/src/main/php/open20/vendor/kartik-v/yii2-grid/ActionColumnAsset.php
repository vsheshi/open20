<?php

/**
 * @package   yii2-grid
 * @version   3.0.3
 */

namespace kartik\grid;

use kartik\base\AssetBundle;

/**
 * Asset bundle for GridView ActionColumn Widget
 *
 * @since 1.0
 */
class ActionColumnAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/kv-grid-action']);
        $this->setupAssets('css', ['css/kv-grid-action']);
        parent::init();
    }
}
