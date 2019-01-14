<?php

class m181120_151701_hide_requested_widgets_PCD_474_475 extends \lispa\amos\core\migration\AmosMigrationWidgets {

    var $classnamesWidgets = [
        'lispa\amos\admin\widgets\icons\WidgetIconMyProfile',
        'lispa\amos\community\widgets\icons\WidgetIconCommunityDashboard',
        'lispa\amos\groups\widgets\icons\WidgetIconGroups',
        'lispa\amos\documenti\widgets\icons\WidgetIconDocumenti',
        'lispa\amos\documenti\widgets\graphics\WidgetGraphicsHierarchicalDocuments',
    ];

    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\admin\widgets\icons\WidgetIconMyProfile::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_DISABLED,
            ],
            [
                'classname' => \lispa\amos\community\widgets\icons\WidgetIconCommunityDashboard::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_DISABLED,
            ],
            [
                'classname' => \lispa\amos\groups\widgets\icons\WidgetIconGroups::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_DISABLED,
            ],
            [
                'classname' => \lispa\amos\documenti\widgets\icons\WidgetIconDocumenti::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_DISABLED,
            ],
            [
                'classname' => \lispa\amos\documenti\widgets\graphics\WidgetGraphicsHierarchicalDocuments::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_DISABLED,
            ],
        ];
    }

    public function afterAddWidgets()
    {
        foreach ($this->classnamesWidgets as $classnameWidget) {
            foreach (\lispa\amos\dashboard\models\AmosWidgets::findAll(['classname' => $classnameWidget, 'module' => 'community']) as $model) {
                $model->status = \lispa\amos\dashboard\models\AmosWidgets::STATUS_DISABLED;
                $model->save(false);
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->widgets = [
            [
                'classname' => \lispa\amos\admin\widgets\icons\WidgetIconMyProfile::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_ENABLED,
            ],
            [
                'classname' => \lispa\amos\community\widgets\icons\WidgetIconCommunityDashboard::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_ENABLED,
            ],
            [
                'classname' => \lispa\amos\groups\widgets\icons\WidgetIconGroups::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_ENABLED,
            ],
            [
                'classname' => \lispa\amos\documenti\widgets\icons\WidgetIconDocumenti::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_ENABLED,
            ],
            [
                'classname' => \lispa\amos\documenti\widgets\graphics\WidgetGraphicsHierarchicalDocuments::className(),
                'update' => true,
                'status' => \lispa\amos\dashboard\models\AmosWidgets::STATUS_ENABLED,
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

    public function afterRemoveWidgets()
    {
        foreach ($this->classnamesWidgets as $classnameWidget) {
            foreach (\lispa\amos\dashboard\models\AmosWidgets::findAll(['classname' => $classnameWidget, 'module' => 'community']) as $model) {
                $model->status = \lispa\amos\dashboard\models\AmosWidgets::STATUS_ENABLED;
                $model->save(false);
            }
        }
        return true;
    }

}
