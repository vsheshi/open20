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
class m180306_171408_pcd20_permissions_groups extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' =>  'PCD_GROUPS',
                'type' => \yii\rbac\Permission::TYPE_PERMISSION,
                'description' => 'Permission for Administrator Groups',
                'ruleName' => null,
                'parent' => ['lispa\amos\community\rules\AuthorRoleRule',
                    'lispa\amos\community\rules\EditorRoleRule',
                    'lispa\amos\community\rules\CommunityManagerRoleRule'],
                'children' => [
                    'GROUPS_CREATE',
                    'GROUPS_DELETE',
                    'GROUPS_READ',
                    'GROUPS_UPDATE',
                    'GROUPSMEMBERS_CREATE',
                    'GROUPSMEMBERS_DELETE',
                    'GROUPSMEMBERS_READ',
                    'GROUPSMEMBERS_UPDATE',
                    'lispa\amos\groups\widgets\icons\WidgetIconGroups'
                ]
            ],

        ];
    }

}
