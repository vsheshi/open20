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

class m170209_155816_add_discussioni_permission_partecipa extends AmosMigrationPermissions
{
    /**
     * Use this function to map permissions, roles and associations between permissions and roles. If you don't need to
     * to add or remove any permissions or roles you have to delete this method.
     */
    protected function setAuthorizations()
    {
        $this->authorizations = [
            [
                'name' => 'DISCUSSIONITOPIC_PARTECIPA',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di partecipare ad una discussione',
                'ruleName' => null,
                'parent' => ['LETTORE_DISCUSSIONI', 'AMMINISTRATORE_DISCUSSIONI']
            ]
        ];
    }
}
