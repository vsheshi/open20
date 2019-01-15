<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

use yii\db\Migration;

class m180606_113801_drop_foreign_key_notify_group extends Migration
{
    public function safeUp()
    {

        $table = $this->db->schema->getTableSchema('documenti_notifiche_preferenze');
        foreach($table->foreignKeys as $key => $value){
            if($key == 'fk_documenti_notifiche_preferenze_user_id_1'){
                $this->execute('SET FOREIGN_KEY_CHECKS = 1');
                $this->dropForeignKey('fk_documenti_notifiche_preferenze_user_id_1', 'documenti_notifiche_preferenze');
                $this->execute('SET FOREIGN_KEY_CHECKS = 0');
            }
        }
        return true;
    }

    public function safeDown() 
    {
        return true;
    }
}
