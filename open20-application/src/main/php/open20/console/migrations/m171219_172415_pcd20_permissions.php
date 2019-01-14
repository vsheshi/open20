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
 * Class m171219_172415_pcd20_permissions
 */
class m171219_172415_pcd20_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
			[
                'name' => 'DOCUMENTI_CREATE',
                'update' => true,
                'newValues' => [
                    'removeParents' => ['CREATORE_DOCUMENTI'],
                    'addParents' => ['ContentCreatorOnDomain']
                ]
            ],
			[
                'name' => 'NEWS_CREATE',
                'update' => true,
                'newValues' => [
                    'removeParents' => ['CREATORE_NEWS'],
                    'addParents' => ['ContentCreatorOnDomain']
                ]
            ],
			[
                'name' => 'EVENT_CREATE',
                'update' => true,
                'newValues' => [
                    'removeParents' => ['EVENTS_CREATOR'],
                    'addParents' => ['ContentCreatorOnDomain']
                ]
            ],
            [
                'name' => 'ContentCreatorOnDomain',
                'update' => true,
                'newValues' => [
                    'addParents' => ['CREATORE_DOCUMENTI', 'CREATORE_NEWS', 'EVENTS_CREATOR' ]
                ]
            ],        
            [
                'name' => 'COMMUNITY_CREATOR',
                'update' => true,
                'newValues' => [
                    'removeParents' => ['VALIDATED_BASIC_USER']
                ]
            ],
			[
                'name' => 'DOCUMENTI_UPDATE',
                'update' => true,
                'newValues' => [
                    'addParents' => ['ContentValidatorOnDomain']
                ]
            ],
			[
                'name' => 'NEWS_UPDATE',
                'update' => true,
                'newValues' => [
                    'addParents' => ['ContentValidatorOnDomain']
                ]
            ],
			[
                'name' => 'EVENT_UPDATE',
                'update' => true,
                'newValues' => [
                    'addParents' => ['ContentValidatorOnDomain']
                ]
            ],
            [
                'name' => 'ContentValidatorOnDomain',
                'update' => true,
                'newValues' => [
                    'addParents' => ['VALIDATED_BASIC_USER']
                ]
            ],
        ];
    }

}
