<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\widgets\icons
 * @category   CategoryName
 */

namespace lispa\amos\myactivities\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\myactivities\AmosMyActivities;
use lispa\amos\myactivities\models\MyActivities;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconMyActivities
 *
 * @package lispa\amos\myactivities\widgets\icons
 */
class WidgetIconMyActivities extends WidgetIcon
{
    /**
     * Init of the class, set of general configurations
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosMyActivities::tHtml('amosmyactivities', 'My activities'));
        $this->setDescription(AmosMyActivities::t('amosmyactivities', 'My activities'));
        $this->setIcon('bullhorn');
        $this->setBulletCount(MyActivities::getCountActivities());
        $this->setUrl(Yii::$app->urlManager->createUrl(['myactivities/my-activities/index']));
        $this->setCode('MYACTIVITIES');
        $this->setModuleName('myactivities');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkPrimary'
        ]));
    }

}