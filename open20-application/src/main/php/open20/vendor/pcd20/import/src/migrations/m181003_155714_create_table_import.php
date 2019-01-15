<?php

use yii\db\Migration;

class m181003_155714_create_table_import extends Migration
{

    const TABLE = 'uploader_import_list';
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) === null)
        {
            $this->createTable(self::TABLE, [
                'id' => \yii\db\Schema::TYPE_PK,
                'name_file' => $this->string()->notNull()->comment('User'),
                'path_log' => $this->string()->comment('Network node'),
                'successfull' => $this->integer(1)->defaultValue(0)->comment('Successful'),
                'created_at' => $this->dateTime()->comment('Created at'),
                'updated_at' =>  $this->dateTime()->comment('Updated at'),
                'deleted_at' => $this->dateTime()->comment('Deleted at'),
                'created_by' =>  $this->integer()->comment('Created by'),
                'updated_by' =>  $this->integer()->comment('Updated at'),
                'deleted_by' =>  $this->integer()->comment('Deleted at'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
        }
        else
        {
            echo "Nessuna creazione eseguita in quanto la tabella esiste gia'";
        }

    }

    public function safeDown()
    {

    }

}
