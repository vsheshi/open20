<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\assets;

use yii\web\AssetBundle;

class OrganizzazioniAsset extends AssetBundle
{
    public $sourcePath = '@lispa/amos/organizzazioni/assets/web';

    public $js = [
    ];
    public $css = [
        'css/style.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset'
    ];

}