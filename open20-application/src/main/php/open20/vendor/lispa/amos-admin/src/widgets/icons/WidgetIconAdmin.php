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
 * Class WidgetIconAdmin
 * @package lispa\amos\admin\widgets\icons
 */
class WidgetIconAdmin extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosAdmin::tHtml('amosadmin', 'Users'));
        $this->setDescription(AmosAdmin::t('amosadmin', 'Users'));
        $this->setIcon('users');
        $this->setUrl(['/admin']);
        $this->setCode('ADMIN_MODULE');
        $this->setModuleName(AmosAdmin::getModuleName());
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey'
        ]));
    }
    
    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        return ArrayHelper::merge($options, ["children" => $this->getWidgetsIcon()]);
    }
    
    /* TEMP */
    public function getWidgetsIcon()
    {
        $widgets = [];
        
        $MyProfile = new WidgetIconMyProfile();
        if ($MyProfile->isVisible()) {
            $widgets[] = $MyProfile->getOptions();
        }
        
        $UserProfile = new WidgetIconUserProfile();
        if ($UserProfile->isVisible()) {
            $widgets[] = $UserProfile->getOptions();
        }
        
        return $widgets;
    }
}
