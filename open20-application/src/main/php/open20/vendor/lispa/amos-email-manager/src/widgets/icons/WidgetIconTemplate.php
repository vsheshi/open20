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

class WidgetIconTemplate extends WidgetIcon
{

    public function init()
    {
        parent::init();

        $this->setLabel(AmosEmail::tHtml('amosemail', 'Template'));
        $this->setDescription(AmosEmail::t('amosemail', 'Template Widget'));

        $this->setIcon('envelope-o');
        //$this->setIconFramework();

        $this->setUrl(Yii::$app->urlManager->createUrl(['email/template']));
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

        return $widgets;
    }
}