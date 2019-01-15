<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */

namespace lispa\amos\emailmanager\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\emailmanager\AmosEmail;
use Yii;
use yii\helpers\ArrayHelper;

class WidgetIconEmailManager extends WidgetIcon
{

    public function init()
    {
        parent::init();

        $this->setLabel(AmosEmail::tHtml('amosemail', 'Email Manager'));
        $this->setDescription(AmosEmail::t('amosemail', 'Email Manager Widget'));

        $this->setIcon('envelope-o');
        //$this->setIconFramework();

        $this->setUrl(Yii::$app->urlManager->createUrl(['email/']));
        $this->setCode('EMAIL_MANAGER_MODULE');
        $this->setModuleName('email');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey'
        ]));

    }

    public function getOptions()
    {
        $options = parent::getOptions();

        //aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
        return ArrayHelper::merge($options, ["children" => $this->getWidgetsIcon()]);
    }

    /* TEMPORANEA */
    public function getWidgetsIcon()
    {
        $widgets = [];

        //istanza di MyProfile
        $Spool = new WidgetIconSpool();
        if ($Spool->isVisible()) {
            $widgets[] = $Spool->getOptions();
        }

        //istanza di UserProfile
        $Template = new WidgetIconTemplate();
        if ($Template->isVisible()) {
            $widgets[] = $Template->getOptions();
        }

        return $widgets;
    }
}