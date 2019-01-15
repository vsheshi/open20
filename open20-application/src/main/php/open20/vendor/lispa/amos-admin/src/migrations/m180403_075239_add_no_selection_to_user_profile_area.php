<?php

use yii\db\Migration;

class m180403_075239_add_no_selection_to_user_profile_area extends Migration
{
    public function safeUp()
    {
        $this->insert('user_profile_area',['name' => 'no selection', 'enabled' => 1, 'order' => 5]);
    }

    public function safeDown()
    {
        $this->delete('user_profile_area',['name' => 'no selection', 'enabled' => 1, 'order' => 5]);
    }

}
