<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\models\base
 * @category   CategoryName
 */

namespace lispa\amos\admin\models\base;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\core\record\AmosRecordAudit;

/**
 * Class UserContact
 *
 * This is the base-model class for table "user_contact".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $contact_id
 * @property string $status
 * @property string $accepted_at
 * @property integer $reminders_count
 * @property string $last_reminder_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @package lispa\amos\admin\models\base
 */
class UserContact extends AmosRecordAudit
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'contact_id', 'status'], 'required'],
            [['status'], 'string'],
            [['reminders_count'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosAdmin::t('amosadmin', 'ID'),
            'user_id' => AmosAdmin::t('amosadmin', 'User ID'),
            'contact_id' => AmosAdmin::t('amosadmin', 'Contact User ID'),
            'status' => AmosAdmin::t('amosadmin', 'Status'),
            'accepted_at' => AmosAdmin::t('amosadmin', 'Accepted at'),
            'reminders_count' => AmosAdmin::t('amosadmin', 'Number of reminders'),
            'last_reminder_at' => AmosAdmin::t('amosadmin', 'Last reminder sent at'),
            'created_at' => AmosAdmin::t('amosadmin', 'Created at'),
            'updated_at' => AmosAdmin::t('amosadmin', 'Updated at'),
            'deleted_at' => AmosAdmin::t('amosadmin', 'Deleted at'),
            'created_by' => AmosAdmin::t('amosadmin', 'Created by'),
            'updated_by' => AmosAdmin::t('amosadmin', 'Updated by'),
            'deleted_by' => AmosAdmin::t('amosadmin', 'Deleted by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\lispa\amos\core\user\User::className(), ['id' => 'user_id']);
    }
}
