<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comments\assets
 * @category   CategoryName
 */

namespace lispa\amos\comuni\assets;

use yii\web\AssetBundle;

/**
 * Class CommentsAsset
 * @package lispa\amos\comuni\assets
 */
class ComuniAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/lispa/amos-comuni/src/assets/web';

    /**
     * @inheritdoc
     */
    public $css = [

    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/comuni_common_js.js'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
