<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\news
 * @category   CategoryName
 */

use yii\db\Migration;
use yii\db\Schema;

class m160912_084737_create_news_allegati extends Migration
{
    const TABLE = '{{%news_allegati}}';
    
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) === null)
        {
            $this->createTable(self::TABLE, [
                'titolo' => Schema::TYPE_STRING . "(255) NOT NULL COMMENT 'Titolo'",
                'descrizione' => Schema::TYPE_TEXT . " COMMENT 'Descrizione'",
                'filemanager_mediafile_id' => Schema::TYPE_INTEGER,
                'news_id' => Schema::TYPE_INTEGER." NOT NULL",
                'created_at' => Schema::TYPE_DATETIME . " NULL DEFAULT NULL COMMENT 'Creato il'",
                'updated_at' => Schema::TYPE_DATETIME . " NULL DEFAULT NULL COMMENT 'Aggiornato il'",
                'deleted_at' => Schema::TYPE_DATETIME . " NULL DEFAULT NULL COMMENT 'Cancellato il'",
                'created_by' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Creato da'",
                'updated_by' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Aggiornato da'",
                'deleted_by' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Cancellato da'",
                'version' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Versione numero'",
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
            $this->addPrimaryKey('',self::TABLE, 'filemanager_mediafile_id');
            $this->addForeignKey('fk_news_allegati_news1_idx', self::TABLE, 'news_id', 'news', 'id');
            $this->addForeignKey('fk_news_allegati_filemanager_mediafile1_idx', self::TABLE, 'filemanager_mediafile_id', 'filemanager_mediafile', 'id');
        }
        else
        {
            echo "Nessuna creazione eseguita in quanto la tabella esiste già";
        }
        
        return true;
    }
    
    public function safeDown()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) !== null)
        {
            $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
            $this->dropTable(self::TABLE);
            $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
        }
        else
        {
            echo "Nessuna cancellazione eseguita in quanto la tabella non esiste";
        }
        
        return true;
    }
}
