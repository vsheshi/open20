<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\chat
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationWidgets;
use lispa\amos\dashboard\models\AmosWidgets;

/**
 * Class m161026_140630_update_amoschat_widgets
 */
class m161026_140630_update_amoschat_widgets extends AmosMigrationWidgets
{
    const MODULE_NAME = 'chat';

    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\chat\widgets\icons\WidgetIconChat::className(),
                'type' => AmosWidgets::TYPE_ICON,
                'module' => self::MODULE_NAME,
                'status' => AmosWidgets::STATUS_ENABLED,
                'child_of' => \lispa\amos\chat\widgets\icons\WidgetIconChat::className(),
                'update' => true
            ]
        ];
    }
}
