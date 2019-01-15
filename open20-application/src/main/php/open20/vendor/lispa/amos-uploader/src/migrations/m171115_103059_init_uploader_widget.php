<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\uploader\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationWidgets;
use lispa\amos\dashboard\models\AmosWidgets;

/**
 * Class m171115_103059_init_uploader_widget
 */
class m171115_103059_init_uploader_widget extends AmosMigrationWidgets
{

    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\uploader\widgets\icons\WidgetIconUploader::className(),
                'type' => AmosWidgets::TYPE_ICON,
                'module' =>'uploader',
                'status' => AmosWidgets::STATUS_ENABLED,
                'child_of' => null,
                'dashboard_visible' => 1,
                'default_order' => 1
            ],
        ];
    }
}