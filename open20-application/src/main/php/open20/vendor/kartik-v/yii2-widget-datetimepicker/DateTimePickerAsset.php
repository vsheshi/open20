<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-datetimepicker
 * @version 1.4.4
 */

namespace kartik\datetime;

use kartik\base\AssetBundle;

/**
 * Asset bundle for [[DateTimePicker]] widget
 *
 * @since 1.0
 */
class DateTimePickerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/bootstrap-datetimepicker', 'css/datetimepicker-kv']);
        $this->setupAssets('js', ['js/bootstrap-datetimepicker']);
        parent::init();
    }
}