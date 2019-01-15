<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti
 * @category   CategoryName
 */

use yii\db\Migration;

/**
 * Class m161130_084737_create_documenti_allegati
 */
class m180308_160437_create_documenti_notifiche_preferenze extends Migration
{
    public $tabella = 'documenti_notifiche_preferenze';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema($this->tabella, true) === null) {
            $this->createTable($this->tabella, [
                'id' => $this->primaryKey(11),
                'documento_parent_id' => $this->integer()->notNull()->comment('Documento'),
                'user_id' => $this->integer()->notNull()->comment('Utente'),
                'group_id' => $this->integer(11)->defaultValue(null)->comment('Gruppo'),
                'created_at' => $this->dateTime()->defaultValue(null)->comment('Creato il'),
                'updated_at' => $this->dateTime()->defaultValue(null)->comment('Aggiornato il'),
                'deleted_at' => $this->dateTime()->defaultValue(null)->comment('Cancellato il'),
                'created_by' => $this->integer(11)->defaultValue(null)->comment('Creato da'),
                'updated_by' => $this->integer(11)->defaultValue(null)->comment('Aggiornato da'),
                'deleted_by' => $this->integer(11)->defaultValue(null)->comment('Cancellato da'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
            $this->addForeignKey('fk_documenti_notifiche_preferenze_documento_parent_id_1', $this->tabella, 'documento_parent_id', 'documenti', 'id');
            $this->addForeignKey('fk_documenti_notifiche_preferenze_user_id_1', $this->tabella, 'user_id', 'user', 'id');
        } else {
            echo "Nessuna creazione eseguita in quanto la tabella esiste già";
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if ($this->db->schema->getTableSchema($this->tabella, true) !== null) {
            $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
            $this->dropTable($this->tabella);
            $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
        } else {
            echo "Nessuna cancellazione eseguita in quanto la tabella non esiste";
        }

        return true;
    }
}
