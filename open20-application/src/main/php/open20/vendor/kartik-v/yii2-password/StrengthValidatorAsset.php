<?php

/**
 * @package   yii2-password
 * @version   1.5.3
 */

namespace kartik\password;

/**
 * Asset bundle for StrengthValidator
 *
 * @since 1.0
 */
class StrengthValidatorAsset extends \kartik\base\AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/strength-validation']);
        parent::init();
    }
}