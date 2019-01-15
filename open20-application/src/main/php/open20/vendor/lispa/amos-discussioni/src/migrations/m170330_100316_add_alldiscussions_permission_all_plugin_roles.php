<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m170330_100316_add_alldiscussions_permission_all_plugin_roles
 */
class m170330_100316_add_alldiscussions_permission_all_plugin_roles extends AmosMigrationPermissions
{
    protected function setAuthorizations()
    {
        $this->authorizations = [
            [
                'name' => 'AMMINISTRATORE_DISCUSSIONI',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Ruolo di amministratore discussioni',
                'ruleName' => null,
                'parent' => ['ADMIN'],
                'dontRemove' => true
            ],
            [
                'name' => lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopicAll::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso widget della dashboard interna delle discussioni',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_DISCUSSIONI', 'CREATORE_DISCUSSIONI', 'LETTORE_DISCUSSIONI', 'FACILITATORE_DISCUSSIONI'],
                'dontRemove' => true
            ],
            [
                'name' => lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopic::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso widget della dashboard interna delle discussioni',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_DISCUSSIONI', 'CREATORE_DISCUSSIONI', 'LETTORE_DISCUSSIONI', 'FACILITATORE_DISCUSSIONI'],
                'dontRemove' => true
            ],
        ];
    }
}
