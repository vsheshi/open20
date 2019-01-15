<?php

use yii\db\Migration;

class m180323_114955_fix_all_sofdeleted_user extends Migration
{

    public function safeUp()
    {
        $this->execute("
            UPDATE user
            SET email = concat('_DELETED_', email)
              , username = concat('_DELETED_', username)
            WHERE deleted_at IS NOT NULL;
        ");
    }

    public function safeDown()
    {
        // DO NOTHING
        return true;
    }

}
