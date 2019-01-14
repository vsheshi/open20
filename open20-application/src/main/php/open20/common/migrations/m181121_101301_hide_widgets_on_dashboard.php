<?php

class m181121_101301_hide_widgets_on_dashboard extends \lispa\amos\core\migration\AmosMigrationWidgets {

    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\admin\widgets\graphics\WidgetGraphicMyProfile::className(),
                'update' => true,
                'dashboard_visible' => false,
            ],
            [
                'classname' => \lispa\amos\community\widgets\graphics\WidgetGraphicsMyCommunities::className(),
                'update' => true,
                'dashboard_visible' => false,
            ],
            [
                'classname' => \lispa\amos\community\widgets\graphics\WidgetGraphicsCommunityReports::className(),
                'update' => true,
                'dashboard_visible' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\admin\widgets\graphics\WidgetGraphicMyProfile::className(),
                'update' => true,
                'dashboard_visible' => true,
            ],
            [
                'classname' => \lispa\amos\community\widgets\graphics\WidgetGraphicsMyCommunities::className(),
                'update' => true,
                'dashboard_visible' => true,
            ],
            [
                'classname' => \lispa\amos\community\widgets\graphics\WidgetGraphicsCommunityReports::className(),
                'update' => true,
                'dashboard_visible' => true,
            ],
        ];
        $allOk = true;
        foreach ($this->widgets as $widgetData) {
            $ok = $this->insertOrUpdateWidget($widgetData);
            if (!$ok) {
                $allOk = false;
            }
        }
        return $allOk;
    }

}
