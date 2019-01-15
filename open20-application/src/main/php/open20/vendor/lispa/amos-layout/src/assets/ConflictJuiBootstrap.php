<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\layout
 * @category   CategoryName
 */

namespace lispa\amos\layout\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset
 * @package lispa\amos\layout\assets
 */

class ConflictJuiBootstrap extends AssetBundle {

    public $sourcePath = '@vendor/lispa/amos-core/views/assets/web';
    
    public $js = [
        'js/conflictJuiBootstrap.js',
    ];
    
    public $depends = [
        'yii\jui\JuiAsset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/resources/base';

        parent::init();
    }

}
