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
use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\discussioni\models\DiscussioniTopic;
use lispa\amos\discussioni\models\search\DiscussioniTopicSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Application as Web;

/**
 * Class WidgetIconDiscussioniTopicCreatedBy
 * This widget can appear on dashboard. This class is used for creation and general configuration.
 * widget that link to the discussion topic of the logged user
 *
 * @package lispa\amos\discussioni\widgets\icons
 */
class WidgetIconDiscussioniTopicAdminAll extends WidgetIcon
{
    /**
     * Init of the class, set of general configurations
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosDiscussioni::tHtml('amosdiscussioni', 'Amministra discussioni'));
        $this->setDescription(AmosDiscussioni::t('amosdiscussioni', 'Elenco di tutte le discussioni'));
        $this->setIcon('comment');
        $this->setUrl(['/discussioni/discussioni-topic/admin-all-discussions']);
        $this->setCode('ADMIN-DISCUSSIONI_TOPIC_ALL');
        $this->setModuleName('discussioni');
        $this->setNamespace(__CLASS__);


        if (Yii::$app instanceof Web) {
            $search = new DiscussioniTopicSearch();
            $notifier = \Yii::$app->getModule('notify');
            $count = 0;
            if ($notifier) {
                $count = $notifier->countNotRead(\Yii::$app->getUser()->id, DiscussioniTopic::className(),
                    $search->buildQuery('admin-all', []));
            }
            $this->setBulletCount($count);
        }


        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));

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
     * TEMPORARY
     */
    public function getWidgetsIcon()
    {
        $widgets = [];

        $WidgetIconDiscussioniTopic = new WidgetIconDiscussioniTopic();
        if ($WidgetIconDiscussioniTopic->isVisible()) {
            $widgets[] = $WidgetIconDiscussioniTopic->getOptions();
        }

        return $widgets;
    }
}