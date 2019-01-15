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
use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopicAll;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Application as Web;

/**
 * Class WidgetIconDiscussioni
 * This widget can appear on dashboard. This class is used for creation and general configuration.
 * widget that link to the discussion dashboard
 *
 * @package lispa\amos\discussioni\widgets\icons
 */
class WidgetIconDiscussioniDashboard extends WidgetIcon
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
        $this->setModuleName('discussioni');
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
        $count = 0;
        try {
            /** @var AmosUserDashboards $userModuleDashboard */
            $userModuleDashboard = AmosUserDashboards::findOne([
                'user_id' => \Yii::$app->user->id,
                'module' => AmosDiscussioni::getModuleName()
            ]);
            if (is_null($userModuleDashboard)) {
                return 0;
            }

            $widgetAll = \Yii::createObject(WidgetIconDiscussioniTopicAll::className());
            $widgetCreatedBy = \Yii::createObject(WidgetIconDiscussioniTopicCreatedBy::className());

            $count = $widgetAll->getBulletCount() + $widgetCreatedBy->getBulletCount();
        }catch (Exception $ex){
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
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
