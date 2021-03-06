<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\events\models\base
 * @category   CategoryName
 */

namespace lispa\amos\events\models\base;

use lispa\amos\events\AmosEvents;
use yii\helpers\ArrayHelper;

/**
 * Class EventLengthMeasurementUnit
 * This is the base-model class for table "event_length_measurement_unit".
 *
 * @property integer $id
 * @property string $title
 * @property string $date_interval_period
 *
 * @property \lispa\amos\events\models\Event[] $events
 *
 * @package lispa\amos\events\models\base
 */
class EventLengthMeasurementUnit extends \lispa\amos\core\record\Record
{
    const UNIT_HOURS = 1;
    const UNIT_DAYS = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_length_measurement_unit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'date_interval_period'], 'safe'],
            [['title', 'date_interval_period'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => AmosEvents::t('amosevents', 'ID'),
            'title' => AmosEvents::t('amosevents', 'Title'),
            'date_interval_period' => AmosEvents::t('amosevents', 'Date Interval Period')
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(\lispa\amos\events\models\Event::className(), ['event_membership_type_id' => 'id']);
    }
}
