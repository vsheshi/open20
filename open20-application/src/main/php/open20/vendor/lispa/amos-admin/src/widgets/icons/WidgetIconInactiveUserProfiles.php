<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\widgets\icons
 * @category   CategoryName
 */

namespace lispa\amos\admin\widgets\icons;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\widget\WidgetIcon;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconInactiveUserProfiles
 * @package lispa\amos\admin\widgets\icons
 */
class WidgetIconInactiveUserProfiles extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosAdmin::tHtml('amosadmin', 'Inactive users'));
        $this->setDescription(AmosAdmin::t('amosadmin', 'List of inactive platform users'));
        $this->setIcon('users');
        $this->setUrl(['/admin/user-profile/inactive-users']);
        $this->setCode('INACTIVE_USERS');
        $this->setModuleName(AmosAdmin::getModuleName());
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey'
        ]));
        $query = new Query();
        $query->from(UserProfile::tableName());
        $query->where(['attivo' => UserProfile::STATUS_DEACTIVATED]);
        $count = $query->count();
        $this->setBulletCount($count);
    }
}
