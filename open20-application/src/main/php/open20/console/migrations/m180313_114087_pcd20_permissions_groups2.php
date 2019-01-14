<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    pcd20\platform\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;

/**
 * Class m171220_155208_pcd20_permissions_2
 */
class m180313_114087_pcd20_permissions_groups2 extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' =>  'PCD_GROUPS',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'lispa\amos\community\rules\AuthorRoleRule',
                        'lispa\amos\community\rules\EditorRoleRule',
                        'lispa\amos\community\rules\CommunityManagerRoleRule'
                    ]
                ]
            ]
        ];
    }

}
