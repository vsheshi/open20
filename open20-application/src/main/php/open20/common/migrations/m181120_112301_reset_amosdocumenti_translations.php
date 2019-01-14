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

/**
 * Class m181120_112301_reset_amosdocumenti_translations
 * Reset amosdocumenti translations for fixing wrong labels displayed on PCD.
 */
class m181120_112301_reset_amosdocumenti_translations extends Migration
{
    public function safeUp()
    {

        $table = $this->db->schema->getTableSchema('documenti_notifiche_preferenze');
        foreach($table->foreignKeys as $key => $value){
            if($key == 'fk_documenti_notifiche_preferenze_user_id_1'){
                $this->execute('SET FOREIGN_KEY_CHECKS = 1');
                $this->dropForeignKey('fk_documenti_notifiche_preferenze_user_id_1', 'documenti_notifiche_preferenze');
                $this->execute("");
                $this->execute('SET FOREIGN_KEY_CHECKS = 0');
            }
        }
        $this->db->createCommand(<<<SQL
            delete 
            FROM `language_translate` where id IN(
            SELECT id
            FROM `language_source`
            WHERE `category` LIKE 'amosdocument%');
            delete
FROM `language_translate` where id IN(
SELECT id
FROM `language_source`
WHERE `category` LIKE '%pcd20import%');
SQL
)->execute();
        return true;
    }

    public function safeDown() 
    {
        echo "m181120_112301_reset_amosdocumenti_translations cannot be reverted";
        return true;
    }
}
