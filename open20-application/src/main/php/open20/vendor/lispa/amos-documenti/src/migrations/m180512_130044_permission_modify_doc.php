<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;
use lispa\amos\documenti\widgets\graphics\WidgetGraphicsHierarchicalDocuments;
use yii\rbac\Permission;

/**
 * Class m171206_173244_documents_hierarchical_graphic_widget_permission
 */
class m180512_130044_permission_modify_doc extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        $prefixStr = 'Permissions for the dashboard for the widget ';
        return [
            [
                'name' => 'DOCUMENTI_UPDATE',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'lispa\amos\community\rules\AuthorRoleRule'
                    ]
                ]
            ]
        ];
    }
}
