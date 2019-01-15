<?php

use yii\db\Migration;

class m170606_073900_add_discussioni_slug extends Migration
{

    public function safeUp()
    {

        $this->addColumn(\lispa\amos\discussioni\models\DiscussioniTopic::tableName(), 'slug',
            $this->text()
                ->null()
                ->after('id')
        );

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn(\lispa\amos\discussioni\models\DiscussioniTopic::tableName(), 'slug');

        return true;
    }
}
