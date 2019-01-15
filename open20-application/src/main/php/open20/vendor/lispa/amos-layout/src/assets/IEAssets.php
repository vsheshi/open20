<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core
 * @category   CategoryName
 */

namespace lispa\amos\layout\assets;

use yii\web\AssetBundle;

class IEAssets extends AssetBundle {

    public $css = [
        //TOODO
    ];

    public $cssOptions = ['condition' => 'IE'];

    public $js = [
        'js/html5shiv.js',              //html5 compatibility
        'js/respond.js',                //ccs3 compatibility
        'js/svg4everybody.legacy.js'    //svg compatibiilty
    ];

    public $jsOptions = ['condition' => 'IE'];

    public $depends = [
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/resources/ie';

        parent::init();
    }

}
