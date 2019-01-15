<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni\migrations
 * @category   CategoryName
 */

use lispa\amos\core\migration\AmosMigrationPermissions;
use lispa\amos\discussioni\models\DiscussioniTopic;
use yii\helpers\ArrayHelper;




class m171113_145859_discussion_remove_workflow_active_lettore extends AmosMigrationPermissions
{

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_ATTIVA,
                'update' => true,
                'newValues' => [
                    'removeParents' => ['LETTORE_DISCUSSIONI']
                ]
            ]
        ];
    }
}
