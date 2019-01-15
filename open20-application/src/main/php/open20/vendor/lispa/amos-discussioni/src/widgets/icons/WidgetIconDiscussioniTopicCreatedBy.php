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
use lispa\amos\notificationmanager\base\NotifyWidgetDoNothing;
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
class WidgetIconDiscussioniTopicCreatedBy extends WidgetIcon
{
    /**
     * Init of the class, set of general configurations
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosDiscussioni::tHtml('amosdiscussioni', 'Discussioni create da me'));
        $this->setDescription(AmosDiscussioni::t('amosdiscussioni', 'Elenco delle discussioni create dall\'utente'));

        $this->setIcon('comment');
        //$this->setIconFramework();

        $this->setUrl(['/discussioni/discussioni-topic/created-by']);
        $this->setCode('DISCUSSIONI_TOPIC_CREATED_BY');
        $this->setModuleName('discussioni');

        if (Yii::$app instanceof Web) {
            $this->setBulletCount($this->makeBulletCount());
        }

        $this->setNamespace(__CLASS__);
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


    /**
     * Make the number to set in the bullet count.
     */
    public function makeBulletCount()
    {
        $modelSearch = new DiscussioniTopicSearch();
        $modelSearch->setNotifier(new NotifyWidgetDoNothing());
        $query = $modelSearch->buildQuery('created-by',[]);
        $count = $query->andWhere([DiscussioniTopic::tableName() . '.status' => DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_BOZZA])->count();
        return $count;
    }
}