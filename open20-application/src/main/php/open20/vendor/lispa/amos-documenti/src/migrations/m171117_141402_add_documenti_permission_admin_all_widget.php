<?php

use yii\db\Migration;
use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

class m171117_141402_add_documenti_permission_admin_all_widget extends AmosMigrationPermissions
{
    protected function setAuthorizations()
    {
        $this->authorizations = [
            [
                'name' => \lispa\amos\documenti\widgets\icons\WidgetIconAdminAllDocumenti::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permission description',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_DOCUMENTI' ],
                'dontRemove' => true
            ],

        ];
    }
}
