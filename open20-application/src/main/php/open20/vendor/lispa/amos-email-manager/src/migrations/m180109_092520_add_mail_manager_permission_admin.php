<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m180109_092520_add_mail_manager_permission_admin
 */
class m180109_092520_add_mail_manager_permission_admin extends AmosMigrationPermissions
{

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {

        return [
            [
                'name' => 'AMMINISTRATORE_EMAIL_MANAGER',
                'update' => true,
                'newValues' => [
                    'addParents' => ['ADMIN']
                ]
            ],

        ];
    }
}

