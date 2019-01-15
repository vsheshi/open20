<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-rangeinput
 * @version 1.0.1
 */

namespace kartik\range;

use Yii;
use yii\helpers\Html;

/**
 * RangeInput widget is an enhanced widget encapsulating the HTML 5 range input.
 *
 * @since 1.0
 */
class RangeInput extends \kartik\base\Html5Input
{
    public $type = 'range';
    public $orientation;
    
    /**
     * @inherit doc
     */
    public function run() {
        if ($this->orientation == 'vertical') {
            Html::addCssClass($this->containerOptions, 'kv-range-vertical');
            $this->html5Options['orient'] = 'vertical';
        }
        parent::run();
    }
}
