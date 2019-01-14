<?php

use lispa\amos\community\rules\CommunityManagerRoleRule;
use lispa\amos\core\migration\AmosMigrationPermissions;

/**
 * Class m180326_103032_add_contents_delete_permissions_to_area_manager
 */
class m180326_103032_add_contents_delete_permissions_to_area_manager extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'NEWS_DELETE',
                'update' => true,
                'newValues' => [
                    'addParents' => [CommunityManagerRoleRule::className()]
                ]
            ],
            [
                'name' => 'DISCUSSIONITOPIC_DELETE',
                'update' => true,
                'newValues' => [
                    'addParents' => [CommunityManagerRoleRule::className()]
                ]
            ],
            [
                'name' => 'DOCUMENTI_DELETE',
                'update' => true,
                'newValues' => [
                    'addParents' => [CommunityManagerRoleRule::className()]
                ]
            ],
            [
                'name' => 'EVENT_DELETE',
                'update' => true,
                'newValues' => [
                    'addParents' => [CommunityManagerRoleRule::className()]
                ]
            ]
        ];
    }
}
