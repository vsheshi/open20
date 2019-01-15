<?php

/**
 * @package   yii2-grid
 * @version   3.0.3
 */

namespace kartik\grid;

use kartik\base\AssetBundle;

/**
 * Asset bundle for GridView EditableColumn Widget
 *
 * @since 1.0
 */
class EditableColumnAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/kv-grid-editable']);
        parent::init();
    }
}
