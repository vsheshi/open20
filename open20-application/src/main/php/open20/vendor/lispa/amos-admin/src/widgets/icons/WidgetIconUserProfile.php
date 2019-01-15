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
use lispa\amos\admin\models\search\UserProfileSearch;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\widget\WidgetIcon;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconUserProfile
 * @package lispa\amos\admin\widgets\icons
 */
class WidgetIconUserProfile extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosAdmin::tHtml('amosadmin', 'All users'));
        $this->setDescription(AmosAdmin::t('amosadmin', 'List of all platform users'));
        $this->setIcon('users');
        $this->setUrl(['/admin/user-profile/index']);
        $this->setCode('ALL_USERS');
        $this->setModuleName(AmosAdmin::getModuleName());
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey'
        ]));
        
        $query = new UserProfileSearch();
        $count = $query->getNewProfilesCount();
        $this->setBulletCount($count);
    }
}
