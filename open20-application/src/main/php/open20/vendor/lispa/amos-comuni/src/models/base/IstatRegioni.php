<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comuni
 * @category   CategoryName
 */

namespace lispa\amos\comuni\models\base;

use Yii;
use lispa\amos\comuni\AmosComuni;

/**
 * This is the base-model class for table "istat_regioni".
 *
 * @property integer $id
 * @property string $nome
 * @property integer $istat_nazioni_id
 *
 * @property \lispa\amos\comuni\models\IstatComuni[] $istatComunis
 * @property \lispa\amos\comuni\models\IstatProvince[] $istatProvinces
 * @property \lispa\amos\comuni\models\IstatNazioni $istatNazioni
 */
class IstatRegioni extends \lispa\amos\core\record\Record
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'istat_regioni';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nome'], 'required'],
            [['id', 'istat_nazioni_id'], 'integer'],
            [['nome'], 'string', 'max' => 255],
            [['istat_nazioni_id'], 'exist', 'skipOnError' => true, 'targetClass' => IstatNazioni::className(), 'targetAttribute' => ['istat_nazioni_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosComuni::t('amoscomuni', 'Codice Istat'),
            'nome' => AmosComuni::t('amoscomuni', 'Regione'),
            'istat_nazioni_id' => AmosComuni::t('amoscomuni', 'Istat Nazioni ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIstatComunis()
    {
        return $this->hasMany(\lispa\amos\comuni\models\IstatComuni::className(), ['istat_regioni_id' => 'id'])->inverseOf('istatRegioni');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIstatProvinces()
    {
        return $this->hasMany(\lispa\amos\comuni\models\IstatProvince::className(), ['istat_regioni_id' => 'id'])->inverseOf('istatRegioni');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIstatNazioni()
    {
        return $this->hasOne(\lispa\amos\comuni\models\IstatNazioni::className(), ['id' => 'istat_nazioni_id'])->inverseOf('istatRegionis');
    }
}
