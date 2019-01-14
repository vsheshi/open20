<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    pcd20-platform
 * @category   CategoryName
 */

/**
 * Class m171216_163215_configure_status_validate
 */
class m180306_150215_disable_organizations extends \yii\db\Migration
{


    public function safeUp()
    {
        $this->update('cwh_config', ['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => 1], ['classname' => 'lispa\amos\organizzazioni\models\Profilo']);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('cwh_config', ['deleted_at' => null , 'deleted_by' => null], ['classname' => 'lispa\amos\organizzazioni\models\Profilo']);
        return true;
    }
}