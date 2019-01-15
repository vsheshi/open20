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

/**
 * Class UserProfileRole
 * This is the base-model class for table "user_profile_role".
 *
 * @property integer $id
 * @property string $name
 * @property integer $enabled
 * @property integer $order
 *
 * @property \lispa\amos\admin\models\UserProfile[] $userProfiles
 *
 * @package lispa\amos\admin\models\base
 */
class UserProfileRole extends \lispa\amos\core\record\Record
{
    const FREELANCE_CONSULTANT = 1;
    const ENTREPRENEUR = 2;
    const TEACHER_RESEARCHER_UNIVERSITY_COLLABORATOR = 3;
    const EMPLOYEE_COLLABORATOR_ENTERPRISE = 4;
    const EMPLOYEE_COLLABORATOR_PUBLIC_ADMINISTRATION = 5;
    const EMPLOYEE_COLLABORATOR_SOCIAL = 6;
    const OTHER = 7;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profile_role';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'enabled', 'order'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['enabled', 'order'], 'integer']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosAdmin::t('amosadmin', 'ID'),
            'name' => AmosAdmin::t('amosadmin', 'Name'),
            'enabled' => AmosAdmin::t('amosadmin', 'Enabled'),
            'order' => AmosAdmin::t('amosadmin', 'Order')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        $modelClass = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile');
        return $this->hasMany($modelClass::className(), ['user_profile_role_id' => 'id']);
    }
}
