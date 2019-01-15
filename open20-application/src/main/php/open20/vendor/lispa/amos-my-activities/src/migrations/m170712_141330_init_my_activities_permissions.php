<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m170712_141330_init_my_activities_permissions
 */
class m170712_141330_init_my_activities_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        $prefixStr = 'Permissions for the dashboard for the widget ';
        return [
            [
                'name' => \lispa\amos\myactivities\widgets\icons\WidgetIconMyActivities::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => $prefixStr . 'WidgetIconMyActivities',
                'parent' => ['VALIDATED_BASIC_USER', 'ADMIN']
            ],
            [
                'name' => 'MYACTIVITIES_READ',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Read permission for model MyActivities',
                'ruleName' => null,
                'parent' => ['VALIDATED_BASIC_USER', 'ADMIN'],
                'dontRemove' => true
            ],
        ];
    }
}
