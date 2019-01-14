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
class m180118_104915_permission_widget_icon_admin extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'lispa\amos\admin\widgets\icons\WidgetIconAdmin',
                'update' => true,
                'newValues' => [
                    'removeParents' => ['BASIC_USER']
                ]
            ],

        ];
    }

}
