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
 * Class WidgetIconMyProfile
 * @package lispa\amos\admin\widgets\icons
 */
class WidgetIconMyProfile extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosAdmin::tHtml('amosadmin', 'Il mio profilo'));
        $this->setDescription(AmosAdmin::t('amosadmin', 'Consente all\'utente di modificare il proprio profilo'));
        $this->setIcon('profile');
        if (!Yii::$app->user->isGuest) {
            $this->setUrl(['/admin/user-profile/update', 'id' => AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => Yii::$app->getUser()->id])->id]);
        }
        $this->setCode('USER_PROFILE');
        $this->setModuleName(AmosAdmin::getModuleName());
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey'
        ]));
    }
}
