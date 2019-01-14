<?php

use lispa\amos\core\migration\AmosMigrationPermissions;

class m180207_112406_remove_admin_privileges_rule extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'PRIVILEGES_MANAGER',
                'update' => true,
                'newValues' => [
                    'removeParents' => ['ADMIN']
                ]
            ],

        ];
    }
}
