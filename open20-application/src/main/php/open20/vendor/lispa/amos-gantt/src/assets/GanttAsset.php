<?php
namespace lispa\amos\gantt\assets;

use yii\helpers\StringHelper;
use yii\web\AssetBundle;

/**
 * Class GanttAsset
 * @package lispa\amos\core\views\assets
 *
 */
class GanttAsset extends AssetBundle
{

    /**
     * @var string the language.
     *
     * It is recommended that you use [IETF language tags](http://en.wikipedia.org/wiki/IETF_language_tag).
     * For example, `en` stands for English, while `en-US` stands for English (United States).
     *
     * If [[language]] is not set, it will be set as language application
     *
     */
    public $language = null;

    public $sourcePath = '@vendor/bower/gantt';

    public $js = [
        'codebase/dhtmlxgantt.js',
        'codebase/ext/dhtmlxgantt_tooltip.js',
        'codebase/ext/dhtmlxgantt_fullscreen.js',
    ];

    public $css = [
        'codebase/dhtmlxgantt.css',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        $language = $this->language ? $this->language : \Yii::$app->language;
        if (isset($language)) {
            $language = StringHelper::byteSubstr($language, 0, 2);
        }
        if (isset($language) && $language != 'en') {
            $this->js[] = "codebase/locale/locale_{$language}.js";
        } else {
            $this->js[] = "codebase/locale/locale.js";
        }

        parent::registerAssetFiles($view);
    }

}