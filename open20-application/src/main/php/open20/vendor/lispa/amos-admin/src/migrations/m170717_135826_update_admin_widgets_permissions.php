<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;

/**
 * Class m170717_135826_update_admin_widgets_permissions
 */
class m170717_135826_update_admin_widgets_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => \lispa\amos\admin\widgets\graphics\WidgetGraphicMyProfile::className(),
                'update' => true,
                'newValues' => [
                    'addParents' => ['ADMIN']
                ]
            ],
            [
                'name' => \lispa\amos\admin\widgets\icons\WidgetIconMyProfile::className(),
                'update' => true,
                'newValues' => [
                    'addParents' => ['ADMIN']
                ]
            ],
            [
                'name' => \lispa\amos\admin\widgets\icons\WidgetIconUserProfile::className(),
                'update' => true,
                'newValues' => [
                    'addParents' => ['ADMIN']
                ]
            ],
            [
                'name' => \lispa\amos\admin\widgets\icons\WidgetIconValidatedUserProfiles::className(),
                'update' => true,
                'newValues' => [
                    'addParents' => ['ADMIN']
                ]
            ],
            [
                'name' => \lispa\amos\admin\widgets\icons\WidgetIconFacilitatorUserProfiles::className(),
                'update' => true,
                'newValues' => [
                    'addParents' => ['ADMIN']
                ]
            ],
            [
                'name' => \lispa\amos\admin\widgets\icons\WidgetIconCommunityManagerUserProfiles::className(),
                'update' => true,
                'newValues' => [
                    'addParents' => ['ADMIN']
                ]
            ]
        ];
    }
}
