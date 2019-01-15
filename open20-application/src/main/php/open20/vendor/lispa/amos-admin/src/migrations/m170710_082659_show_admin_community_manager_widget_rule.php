<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\migrations
 * @category   CategoryName
 */

use lispa\amos\admin\rules\ShowCommunityManagerWidgetRule;
use lispa\amos\core\migration\AmosMigrationPermissions;

/**
 * Class m170710_082659_show_admin_community_manager_widget_rule
 */
class m170710_082659_show_admin_community_manager_widget_rule extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => \lispa\amos\admin\widgets\icons\WidgetIconCommunityManagerUserProfiles::className(),
                'update' => true,
                'newValues' => [
                    'ruleName' => ShowCommunityManagerWidgetRule::className()
                ],
                'oldValues' => [
                    'ruleName' => null
                ]
            ]
        ];
    }
}
