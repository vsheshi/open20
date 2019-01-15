<?php

/**
 * @package yii2-sortable
 * @version 1.2.0
 */

namespace kartik\sortable;

/**
 * Sortable bundle for \kartik\sortable\Sortable
 *
 * @since 1.0
 */
class SortableAsset extends \kartik\base\AssetBundle
{

    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/kv-sortable']);
        $this->setupAssets('js', ['js/html.sortable']);
        parent::init();
    }

}