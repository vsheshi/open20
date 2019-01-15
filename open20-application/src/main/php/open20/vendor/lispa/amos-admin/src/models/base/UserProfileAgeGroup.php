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
 * Class UserProfileAgeGroup
 * This is the base-model class for table "user_profile_age_group".
 *
 * @property integer $id
 * @property string $age_group
 * @property integer $enabled
 * @property integer $order
 *
 * @property \lispa\amos\admin\models\UserProfile[] $userProfiles
 *
 * @package lispa\amos\admin\models\base
 */
class UserProfileAgeGroup extends \lispa\amos\core\record\Record
{
    const AGE_GROUP_18_25 = 1;
    const AGE_GROUP_36_35 = 2;
    const AGE_GROUP_36_45 = 3;
    const AGE_GROUP_46_55 = 4;
    const AGE_GROUP_56_65 = 5;
    const AGE_GROUP_OVER_65 = 6;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profile_age_group';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['age_group', 'enabled', 'order'], 'safe'],
            [['age_group'], 'string', 'max' => 255],
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
            'age_group' => AmosAdmin::t('amosadmin', 'Age Group'),
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
        return $this->hasMany($modelClass::className(), ['user_profile_age_group_id' => 'id']);
    }
}
