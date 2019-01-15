<?php

/**
 * @package yii2-icons
 * @version 1.4.4
 */

namespace kartik\icons;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Asset bundle for FontAwesome icon set. Uses client assets (CSS, images, and fonts) from font-awesome repository.
 *
 *
 * @since 1.0
 */
class FontAwesomeProAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $basePath = '@webroot';
    /**
     * @inheritdoc
     */
    public $baseUrl = '@web';
    /**
     * @inheritdoc
     */
    public $js = [
        'js/fontawesome-all.min.js',  //Font Awesome 5 Pro is subscriber only; user has to copy file to @web/js
    ];
    /**
     * @inheritdoc
     */
    public $jsOptions = [
        'position' => View::POS_HEAD,
        'defer' => true,
    ];
}
