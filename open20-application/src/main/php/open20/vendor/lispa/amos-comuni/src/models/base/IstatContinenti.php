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
 * This is the base-model class for table "istat_continenti".
 *
 * @property integer $id
 * @property string $nome
 *
 * @property \lispa\amos\comuni\models\IstatNazioni[] $istatNazionis
 */
class IstatContinenti extends \lispa\amos\core\record\Record
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'istat_continenti';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
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
            'nome' => AmosComuni::t('amoscomuni', 'Continente'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIstatNazionis()
    {
        return $this->hasMany(\lispa\amos\comuni\models\IstatNazioni::className(), ['istat_continenti_id' => 'id'])->inverseOf('istatContinenti');
    }
}
