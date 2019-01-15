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
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconFacilitatorUserProfiles
 * @package lispa\amos\admin\widgets\icons
 */
class WidgetIconFacilitatorUserProfiles extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosAdmin::tHtml('amosadmin', 'Facilitators'));
        $this->setDescription(AmosAdmin::t('amosadmin', 'List of users with facilitator role'));
        $this->setIcon('users');
        $this->setUrl(['/admin/user-profile/facilitator-users']);
        $this->setCode('FACILITATOR_USERS');
        $this->setModuleName(AmosAdmin::getModuleName());
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey'
        ]));
    }
}
