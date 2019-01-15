<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\widgets\icons;

use lispa\amos\organizzazioni\Module;
use lispa\amos\core\widget\WidgetIcon;
use Yii;
use yii\helpers\ArrayHelper;

class WidgetIconProfilo extends WidgetIcon {
    
 public function init()
    {
        parent::init();

        $this->setLabel(Module::t('amosorganizzazioni', 'Organizzazioni'));
        $this->setDescription(Module::t('amosorganizzazioni', 'Consente all\'utente di modificare l\'entità Organizzazioni'));

        $this->setIcon('linentita');
       
        if(!Yii::$app->user->isGuest){
            $this->setUrl(\Yii::$app->urlManager->createUrl(['/organizzazioni/profilo/index']));
        }

        $this->setCode('PROFILO');
        $this->setModuleName('organizzazioni');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-lightBase'
        ]));
    }
    
}
