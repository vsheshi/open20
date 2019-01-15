<?php

/**
 * @package   yii2-grid
 * @version   3.1.2
 */

namespace kartik\grid;

use kartik\base\AssetBundle;

/**
 * Asset bundle for GridView Widget (for toggling data)
 *
 * @since 1.0
 */
class GridToggleDataAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/kv-grid-toggle']);
        parent::init();
    }
}
