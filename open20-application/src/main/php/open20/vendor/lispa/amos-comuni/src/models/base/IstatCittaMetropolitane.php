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
 * This is the base-model class for table "istat_citta_metropolitane".
 *
 * @property integer $id
 * @property string $nome
 *
 * @property \lispa\amos\comuni\models\IstatProvince[] $istatProvinces
 */
class IstatCittaMetropolitane extends \lispa\amos\core\record\Record
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'istat_citta_metropolitane';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'nome'], 'required'],
            [['id'], 'integer'],
            [['nome'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosComuni::t('amoscomuni', 'Codice Istat'),
            'nome' => AmosComuni::t('amoscomuni', 'Città Metropolitana'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIstatProvinces()
    {
        return $this->hasMany(\lispa\amos\comuni\models\IstatProvince::className(), ['istat_citta_metropolitane_id' => 'id'])->inverseOf('istatCittaMetropolitane');
    }
}
