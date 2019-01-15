<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

namespace lispa\amos\discussioni\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\dashboard\models\AmosUserDashboards;
use lispa\amos\dashboard\models\AmosUserDashboardsWidgetMm;
use lispa\amos\dashboard\models\AmosWidgets;
use lispa\amos\discussioni\AmosDiscussioni;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Application as Web;

/**
 * Class WidgetIconDiscussioni
 * This widget can appear on dashboard. This class is used for creation and general configuration.
 * widget that link to the discussion dashboard
 * @deprecated
 * @package lispa\amos\discussioni\widgets\icons
 */
class WidgetIconDiscussioni extends WidgetIcon
{
    /**
     * Init of the class, set of general configurations
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosDiscussioni::tHtml('amosdiscussioni', 'Discussioni'));
        $this->setDescription(AmosDiscussioni::t('amosdiscussioni', 'Modulo discussioni'));
        $this->setIcon('comment');
        $this->setUrl(['/discussioni']);
        $this->setCode('DISCUSSIONI_MODULE_001');
        $this->setModuleName('discussioni-dashboard');
        $this->setNamespace(__CLASS__);
        if (Yii::$app instanceof Web) {
            $this->setBulletCount($this->getBulletCountChildWidgets());
        }
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }


    /**
     *
     * @return int - the sum of bulletCount internal widget
     *
     */
    private function getBulletCountChildWidgets()
    {
        /** @var AmosUserDashboards $userModuleDashboard */
        $userModuleDashboard = AmosUserDashboards::findOne([
            'user_id' => \Yii::$app->user->id,
            'module' => AmosDiscussioni::getModuleName()
        ]);
        if (is_null($userModuleDashboard)) return 0;

        $listWidgetChild = $userModuleDashboard->amosUserDashboardsWidgetMms;
        if (is_null($listWidgetChild)) return 0;
        $count = 0;
        /** @var AmosUserDashboardsWidgetMm $widgetChild */
        foreach ($listWidgetChild as $widgetChild) {
            if ($widgetChild->amos_widgets_classname != $this->getNamespace()) {
                $amosWidget = AmosWidgets::findOne(['classname' => $widgetChild->amos_widgets_classname]);
                if ($amosWidget->type == AmosWidgets::TYPE_ICON) {
                    $widget = \Yii::createObject($widgetChild->amos_widgets_classname);
                    $val = $widget->getBulletCount();

                    $bulletCount = empty($val) ? 0 : $val;
                    $count = $count + $bulletCount;
                }
            }
        }
        return $count;
    }

    /**
     * all widgets added to the container object retrieved from the module controller
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        return ArrayHelper::merge($options, ["children" => $this->getWidgetsIcon()]);
    }

    /**
     * @todo TEMPORARY
     */
    public function getWidgetsIcon()
    {
        $widgets = [];

        $WidgetIconDiscussioniTopicc = new WidgetIconDiscussioniTopic();

        if ($WidgetIconDiscussioniTopicc->isVisible()) {
            $widgets[] = $WidgetIconDiscussioniTopicc->getOptions();
        }

        $WidgetIconDiscussioniTopicCreatedBy = new WidgetIconDiscussioniTopicCreatedBy();
        if ($WidgetIconDiscussioniTopicCreatedBy->isVisible()) {
            $widgets[] = $WidgetIconDiscussioniTopicCreatedBy->getOptions();
        }


        return $widgets;
    }
}
