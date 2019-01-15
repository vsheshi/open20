<?php

use lispa\amos\core\migration\AmosMigrationPermissions;
use lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard;

class m170601_100329_add_document_basic_user_dashboard_widget extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {

        return [
            [
                'name' => WidgetIconDocumentiDashboard::className(),
                'update' => true,
                'newValues' => [
                    'addParents' => ['BASIC_USER']
                ]
            ],

        ];
    }
}