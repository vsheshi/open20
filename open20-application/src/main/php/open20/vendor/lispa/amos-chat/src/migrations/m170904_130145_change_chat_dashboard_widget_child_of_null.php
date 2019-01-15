<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\chat\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationWidgets;

/**
 * Class m170904_130145_change_chat_dashboard_widget_child_of_null
 */
class m170904_130145_change_chat_dashboard_widget_child_of_null extends AmosMigrationWidgets
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
                'child_of' => null,
                'update' => true
            ]
        ];
    }
}
