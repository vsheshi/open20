<?php

/**
 * @package   yii2-krajee-base
 * @version   1.8.9
 */

namespace kartik\base;

/**
 * Base module class for Krajee extensions
 *
 * @since 1.8.9
 */
class Module extends \yii\base\Module
{
    use TranslationTrait;

    /**
     * @var array the the internalization configuration for this widget
     */
    public $i18n = [];

    /**
     * @var string translation message file category name for i18n
     */
    protected $_msgCat = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->initI18N();
    }
}
