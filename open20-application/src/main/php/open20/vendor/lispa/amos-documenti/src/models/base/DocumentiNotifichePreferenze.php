<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\models\base
 * @category   CategoryName
 */

namespace lispa\amos\documenti\models\base;

use lispa\amos\core\user\User;
use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\groups\models\Groups;
use lispa\amos\notificationmanager\record\NotifyRecord;
use yii\helpers\ArrayHelper;

/**
 * Class Documenti
 *
 * This is the base-model class for table "documenti_notifiche_preferenze".
 *
 * @property integer $id
 * @property integer $documento_parent_id
 * @property integer $groups_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \lispa\amos\documenti\models\Documenti $documentoParent
 * @property User $user
 * @property Groups $groups
 *
 * @package lispa\amos\documenti\models\base
 */
class DocumentiNotifichePreferenze extends NotifyRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'documenti_notifiche_preferenze';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => AmosDocumenti::t('amosdocumenti', 'Id'),
            'created_at' => AmosDocumenti::t('amosdocumenti', 'Creato il'),
            'updated_at' => AmosDocumenti::t('amosdocumenti', 'Aggiornato il'),
            'deleted_at' => AmosDocumenti::t('amosdocumenti', 'Cancellato il'),
            'created_by' => AmosDocumenti::t('amosdocumenti', 'Creato da'),
            'updated_by' => AmosDocumenti::t('amosdocumenti', 'Aggiornato da'),
            'deleted_by' => AmosDocumenti::t('amosdocumenti', 'Cancellato da'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentoParent()
    {
        return $this->hasOne(\lispa\amos\documenti\models\Documenti::className(), ['id' => 'documento_parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasOne(Groups::className(), ['id' => 'groups_id']);
    }

}
