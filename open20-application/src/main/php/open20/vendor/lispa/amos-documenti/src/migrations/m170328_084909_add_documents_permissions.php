<?php

use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

class m170328_084909_add_documents_permissions extends AmosMigrationPermissions
{
    protected function setAuthorizations()
    {
        $this->authorizations = [
            [
                'name' => 'AMMINISTRATORE_DOCUMENTI',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Amministratore documenti',
                'ruleName' => null,
                'parent' => ['ADMIN'],
                'dontRemove' => true
            ],
        ];
    }
}
