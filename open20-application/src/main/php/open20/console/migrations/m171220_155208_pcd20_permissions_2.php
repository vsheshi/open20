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
class m171220_155208_pcd20_permissions_2 extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'COMMUNITY_CREATE',
                'update' => true,
                'newValues' => [
                    'addParents' => ['CreateSubcommunities']
                ]
            ],
            [
                'name' => 'CreateSubcommunities',
                'update' => true,
                'newValues' => [
                    'addParents' => ['COMMUNITY_MEMBER' ]
                ]
            ],
            [
                'name' => 'DISCUSSIONITOPIC_CREATE',
                'update' => true,
                'newValues' => [
                    'removeParents' => ['CREATORE_DISCUSSIONI'],
                    'addParents' => ['ContentCreatorOnDomain']
                ]
            ],
            [
                'name' => 'ContentCreatorOnDomain',
                'update' => true,
                'newValues' => [
                    'addParents' => ['CREATORE_DISCUSSIONI' ]
                ]
            ],
            [
                'name' => 'DISCUSSIONITOPIC_UPDATE',
                'update' => true,
                'newValues' => [
                    'addParents' => ['ContentValidatorOnDomain']
                ]
            ],
        ];
    }

}
