<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos-report
 * @category   Model
 */


namespace lispa\amos\report\models;

use lispa\amos\notificationmanager\behaviors\NotifyBehavior;
use yii\helpers\ArrayHelper;

class Report extends \lispa\amos\report\models\base\Report
{
    /**
     */
    public function init()
    {
        parent::init();
    }

    public function afterFind()
    {
        parent::afterFind();
    }
    /**
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'NotifyBehavior' => [
                'class' => NotifyBehavior::className(),
                'conditions' => [],
            ]
        ]);
    }

}