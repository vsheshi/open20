<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

use yii\db\Migration;

class m171214_100337_add_cwh_organizzazioni extends Migration
{
    private $tablename = 'cwh_config';

    public function safeUp()
    {

        try{
            $this->insert($this->tablename,
            [
                'id' => 7,
                'classname' => lispa\amos\organizzazioni\models\Profilo::className(),
                'tablename' => 'profilo',
                'raw_sql' => "select concat('profilo-',`profilo`.`id`) AS `id`, 7 AS `cwh_config_id`, `profilo`.`id` AS `record_id`, 'lispa\\amos\organizzazioni\\models\\Profilo' AS `classname`, 1 AS `visibility`, `profilo`.`created_at` AS `created_at`, `profilo`.`updated_at` AS `updated_at`, `profilo`.`deleted_at` AS `deleted_at`, `profilo`.`created_by` AS `created_by`, `profilo`.`updated_by` AS `updated_by`, `profilo`.`deleted_by` AS `deleted_by` from `profilo`"
            ]);

        }catch(\yii\base\Exception $ex){
            echo $ex->getMessage();
            return false;
        }
        return true;
    }

    public function safeDown()
    {
        return true;
    }


    
}
