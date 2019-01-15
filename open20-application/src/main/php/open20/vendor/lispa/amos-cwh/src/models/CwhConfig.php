<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\models;

use lispa\amos\cwh\AmosCwh;

/**
 * This is the model class for table "cwh_config".
 */
class CwhConfig extends \lispa\amos\cwh\models\base\CwhConfig
{
    
    const RAW_SQL_EXAMPLE = 'select 
concat(\'tablename-\',`tablename`.`id`) AS `id`,
cwh_config_id AS `cwh_config_id`,
`tablename`.`id` AS `record_id`,
\'ModelClassName\' AS `classname`, 
-- use \'\\\\\' separator in classname to get sql code ok
1 AS `visibility`, 
-- 1 if no constraint on visibility of the network, otherwise put your condition 
-- eg. (CASE `tablename`.`visibility_ttribute` WHEN 1 THEN 1 ELSE  0 END) AS `visibility`,
`tablename`.`created_at` AS `created_at`,
`tablename`.`updated_at` AS `updated_at`,
`tablename`.`deleted_at` AS `deleted_at`,
`tablename`.`created_by` AS `created_by`,
`tablename`.`updated_by` AS `updated_by`,
`tablename`.`deleted_by` AS `deleted_by` 

from `tablename`';

    public static function getConfig($className)
    {
        return self::findOne(['classname' => $className]);
    }

    public static function getConfigs()
    {
        return self::find()->all();
    }

    public function attributeHints()
    {
        return [
            'raw_sql' => AmosCwh::t('amoscwh', '#raw_sql_hint') . self::RAW_SQL_EXAMPLE
        ];
    }

}
