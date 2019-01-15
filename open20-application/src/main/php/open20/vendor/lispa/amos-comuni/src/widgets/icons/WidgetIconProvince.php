<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comuni
 * @category   CategoryName
 */

namespace lispa\amos\comuni\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use Yii;
use yii\helpers\ArrayHelper;
use lispa\amos\comuni\AmosComuni;

class WidgetIconProvince extends WidgetIcon
{

    public function init()
    {
        parent::init();

        $this->setLabel(AmosComuni::t('amoscomuni', 'Elenco province'));
        $this->setDescription(AmosComuni::t('amoscomuni', 'Consente di modificare le province'));

        $this->setIcon('balance');
        $this->setIconFramework('am');
        $this->setUrl(Yii::$app->urlManager->createUrl(['/comuni/istat-province/index']));
        $this->setCode('ISTAT_PROVINCE');
        $this->setModuleName('comuni');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-grey'
        ]));
    }

}