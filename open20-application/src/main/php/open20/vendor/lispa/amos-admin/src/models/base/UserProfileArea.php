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
 * Class UserProfileArea
 * This is the base-model class for table "user_profile_area".
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
class UserProfileArea extends \lispa\amos\core\record\Record
{
    const PURCHASING = 1;
    const ADMINISTRATION_FINANCE = 2;
    const COMMERCIAL = 3;
    const INFORMATION_SYSTEMS = 4;
    const LOGISTIC = 5;
    const MARKETING = 6;
    const COMMUNICATION = 7;
    const ORGANIZATION_QUALITY = 8;
    const PRODUCTION = 9;
    const RESEARCH_AND_DEVELOPMENT = 10;
    const HUMAN_RESOURCES = 11;
    const OTHER = 12;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profile_area';
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
        return $this->hasMany($modelClass::className(), ['user_profile_area_id' => 'id']);
    }
}
