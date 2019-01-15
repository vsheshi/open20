<?php

use lispa\amos\core\migration\AmosMigrationPermissions;
use lispa\amos\news\models\News;
use yii\helpers\ArrayHelper;
use lispa\amos\documenti\models\Documenti;


class m171114_133632_documents_remove_workflow_active_lettore extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO,
                'update' => true,
                'newValues' => [
                    'removeParents' => ['LETTORE_DOCUMENTI']
                ]
            ]
        ];
    }
}
