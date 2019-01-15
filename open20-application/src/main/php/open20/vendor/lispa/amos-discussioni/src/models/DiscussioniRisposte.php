<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

namespace lispa\amos\discussioni\models;

use lispa\amos\discussioni\models\base\DiscussioniRisposte as DiscussioniRisposteBase;
use yii\db\BaseActiveRecord;

/**
 * Class DiscussioniRisposte
 * This is the model class for table "discussioni_risposte".
 * @package lispa\amos\discussioni\models
 * @deprecated from version 1.5.
 */
class DiscussioniRisposte extends DiscussioniRisposteBase
{
    /**
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        $this->getDiscussioniTopic()->one()->save(FALSE);
        //TODO Disabilito la spedizione delle notifiche in una situazione standard
        parent::afterSave($insert, $changedAttributes);
    }

}
