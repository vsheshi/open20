<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

class m170612_000001_bypass_cwhconfiguration extends \yii\db\Migration
{
    public function up()
    {
        $sql = '
		INSERT INTO `cwh_config_contents` (`id`, `classname`, `label`, `status_attribute`, `status_value`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
		(3,	\'lispa\\\amos\\\news\\\models\\\News\',	\'News\',	\'status\',	\'NewsWorkflow/BOZZA\',	\'2017-06-12 09:58:40\',	\'2017-06-12 09:58:40\',	NULL,	1,	1,	NULL),
		(4,	\'lispa\\\amos\\\discussioni\\\models\\\DiscussioniTopic\',	\'DiscussioniTopic\',	\'status\',	\'DiscussioniTopicWorkflow/BOZZA\',	\'2017-06-12 09:58:53\',	\'2017-06-12 09:58:53\',	NULL,	1,	1,	NULL),
		(5,	\'lispa\\\amos\\\documenti\\\models\\\Documenti\',	\'Documenti\',	\'status\',	\'DocumentiWorkflow/BOZZA\',	\'2017-06-12 09:59:09\',	\'2017-06-12 09:59:09\',	NULL,	1,	1,	NULL);
        ';
        $this->execute($sql);

    }

    public function down()
    {


    }
}

