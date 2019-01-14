<?php
use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m180920_163942_social_idm_user_permissions*/
class m180920_163942_social_idm_user_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'SOCIALIDMUSER_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model SocialIdmUser',
                    'ruleName' => null,
                    'parent' => ['ADMIN']
                ],
                [
                    'name' =>  'SOCIALIDMUSER_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model SocialIdmUser',
                    'ruleName' => null,
                    'parent' => ['ADMIN']
                    ],
                [
                    'name' =>  'SOCIALIDMUSER_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model SocialIdmUser',
                    'ruleName' => null,
                    'parent' => ['ADMIN']
                ],
                [
                    'name' =>  'SOCIALIDMUSER_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model SocialIdmUser',
                    'ruleName' => null,
                    'parent' => ['ADMIN']
                ],

            ];
    }
}
