<?php

/**
 * @package   yii2-grid
 * @version   3.1.2
 */

namespace kartik\grid;

use kartik\base\AssetBundle;

/**
 * Asset bundle for GridView Widget (for perfect scrollbar)
 *
 * @since 1.0
 */
class GridPerfectScrollbarAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/perfect-scrollbar', 'css/perfect-scrollbar-kv']);
        $this->setupAssets('js', ['js/perfect-scrollbar.jquery']);
        parent::init();
    }
}
