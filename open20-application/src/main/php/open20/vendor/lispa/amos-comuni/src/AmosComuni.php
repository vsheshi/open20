<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comuni
 * @category   CategoryName
 */

namespace lispa\amos\comuni;

use lispa\amos\core\module\AmosModule;
use lispa\amos\comuni\widgets\icons\WidgetIconAmmComuni;
use lispa\amos\comuni\widgets\icons\WidgetIconComuni;
use lispa\amos\comuni\widgets\icons\WidgetIconProvince;

/**
 * AmosComuni module definition class
 */
class AmosComuni extends AmosModule {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\comuni\controllers';
    public $newFileMode = 0666;
    public $newDirMode = 0777;
    public $name = 'comuni';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        // custom initialization code goes here
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));
    }

    protected function getDefaultModels() {
        return [];
    }

    /**
     *
     * @return string
     */
    public static function getModuleName() {
        return 'comuni';
    }

    public function getWidgetGraphics() {
        return NULL;
    }

    public function getWidgetIcons() {
        return [
            WidgetIconAmmComuni::className(),
            WidgetIconComuni::className(),
            WidgetIconProvince::className(),
        ];
    }

}
