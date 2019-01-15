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
use lispa\amos\core\widget\WidgetIcon;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconCommunityManagerUserProfiles
 * @package lispa\amos\admin\widgets\icons
 */
class WidgetIconCommunityManagerUserProfiles extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosAdmin::tHtml('amosadmin', 'Community Managers'));
        $this->setDescription(AmosAdmin::t('amosadmin', 'List of community manager users'));
        $this->setIcon('users');
        $this->setUrl(['/admin/user-profile/community-manager-users']);
        $this->setCode('COMMUNITY_MANAGER_USERS');
        $this->setModuleName('admin');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey'
        ]));
    }
}
