<?php
use lispa\amos\core\migration\AmosMigrationWidgets;
use lispa\amos\dashboard\models\AmosWidgets;


/**
 * Class m171031_120002_add_amos_widget_variazioni_comuni*/
class m171031_120002_add_amos_widget_variazioni_comuni extends AmosMigrationWidgets
{
    const MODULE_NAME = 'comuni';

    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\comuni\widgets\icons\WidgetIconVariazioniComuni::className(),
                'type' => AmosWidgets::TYPE_ICON,
                'module' => self::MODULE_NAME,
                //the widget is visible ONLY for second level dashboard 'comuni'
                'dashboard_visible' => 0,
                'child_of' => \lispa\amos\comuni\widgets\icons\WidgetIconVariazioniComuni::className(),
            ]
        ];
    }
}