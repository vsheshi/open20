<?php


use lispa\amos\core\migration\AmosMigrationPermissions;
use lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniDashboard;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;

class m170601_080710_add_discussion_community_dashboard extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => WidgetIconDiscussioniDashboard::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permission to discussion dashboard',
                'parent' => ['BASIC_USER']
            ],
        ];
    }
}
