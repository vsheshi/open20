<?php

use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

class m180418_190642_permission_delete_comm extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'lispa\amos\community\rules\DeleteOwnCommunitiesRule',
                'update' => true,
                'newValues' => [
                    'addParents' => ['BASIC_USER']
                ]
            ],
        ];
    }
}

