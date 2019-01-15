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

use lispa\amos\organizzazioni\Module;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "profilo_user_mm".
 *
 * @property integer $id
 * @property integer $profilo_id
 * @property integer $user_id
 * @property string $status
 * @property string $role
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 */
class ProfiloUserMm extends \lispa\amos\core\record\Record
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profilo_user_mm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profilo_id', 'user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['status', 'role'], 'string', 'max' => 255],
            [['profilo_id'], 'exist', 'skipOnError' => true, 'targetClass' => lispa\amos\organizzazioni\models\Profilo::className(), 'targetAttribute' => ['profilo_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \lispa\amos\core\user\User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => Module::t('amosorganizzazioni', 'ID'),
            'profilo_id' => Module::t('amosorganizzazioni', 'Organizzazione'),
            'user_id' => Module::t('amosorganizzazioni', 'Utente'),
            'status' => Module::t('amosorganizzazioni', 'Stato'),
            'role' => Module::t('amosorganizzazioni', 'Ruolo'),
            'created_at' => Module::t('amosorganizzazioni', 'Creato il'),
            'updated_at' => Module::t('amosorganizzazioni', 'Aggiornato il'),
            'deleted_at' => Module::t('amosorganizzazioni', 'Cancellato il'),
            'created_by' => Module::t('amosorganizzazioni', 'Creato da'),
            'updated_by' => Module::t('amosorganizzazioni', 'Aggiornato da'),
            'deleted_by' => Module::t('amosorganizzazioni', 'Cancellato da'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfilo()
    {
        return $this->hasOne(\lispa\amos\organizzazioni\models\Profilo::className(), ['id' => 'profilo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\lispa\amos\core\user\User::className(), ['id' => 'user_id']);
    }
}
