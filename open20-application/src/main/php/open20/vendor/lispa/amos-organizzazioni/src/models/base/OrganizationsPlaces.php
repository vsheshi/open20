<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\models\base;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "organizzazioni_places".
 *
 * @property string $place_id
 * @property string $place_response
 * @property string $place_type
 * @property string $country
 * @property string $region
 * @property string $province
 * @property string $postal_code
 * @property string $city
 * @property string $address
 * @property string $street_number
 * @property string $latitude
 * @property string $longitude
 */
class OrganizationsPlaces extends \lispa\amos\core\record\Record
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organizzazioni_places';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['place_id'], 'required'],
            [['place_response'], 'string'],
            [['place_id', 'place_type', 'country', 'region', 'province', 'city', 'address', 'latitude', 'longitude','postal_code', 'street_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'place_id' => Yii::t('amosorganizzazioni', 'Codice recupero place'),
            'place_response' => Yii::t('amosorganizzazioni', 'Risposta'),
            'place_type' => Yii::t('amosorganizzazioni', 'Tipologia di recupero dati'),
            'country' => Yii::t('amosorganizzazioni', 'Paese'),
            'region' => Yii::t('amosorganizzazioni', 'Regione'),
            'province' => Yii::t('amosorganizzazioni', 'Provincia'),
            'postal_code' => Yii::t('amosorganizzazioni', 'CAP'),
            'city' => Yii::t('amosorganizzazioni', 'Città'),
            'address' => Yii::t('amosorganizzazioni', 'Via/Piazza'),
            'street_number' => Yii::t('amosorganizzazioni', 'Numero civico'),
            'latitude' => Yii::t('amosorganizzazioni', 'Latitudine'),
            'longitude' => Yii::t('amosorganizzazioni', 'Longitudine'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizations()
    {
        return $this->hasMany(\lispa\amos\organizzazioni\models\Profilo::className(), ['operational_headquarters_place_id' => 'place_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizations0()
    {
        return $this->hasMany(\lispa\amos\organizzazioni\models\Profilo::className(), ['registered_office_place_id' => 'place_id']);
    }
}
