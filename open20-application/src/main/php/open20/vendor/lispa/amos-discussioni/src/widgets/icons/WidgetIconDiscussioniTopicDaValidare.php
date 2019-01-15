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
 * Class WidgetIconDiscussioniTopicDaValidare
 * This widget can appear on dashboard. This class is used for creation and general configuration.
 * widget that link to the discussion topic, list of threads that are to be validated
 *
 * @package lispa\amos\discussioni\widgets\icons
 */
class WidgetIconDiscussioniTopicDaValidare extends WidgetIcon
{
    /**
     * Init of the class, set of general configurations
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosDiscussioni::tHtml('amosdiscussioni', 'Discussioni da validare'));
        $this->setDescription(AmosDiscussioni::t('amosdiscussioni', 'Elenco delle discussioni che sono da vallidare'));
        $this->setIcon('comment');
        $this->setUrl(['/discussioni/discussioni-topic/to-validate-discussions']);
        $this->setCode('DISCUSSIONI_TOPIC_DA_VALIDARE');
        $this->setModuleName('discussioni');

        if (Yii::$app instanceof Web) {
            $search = new DiscussioniTopicSearch();
            $notifier = \Yii::$app->getModule('notify');
            $count = 0;
            if ($notifier) {
                $count = $notifier->countNotRead(\Yii::$app->getUser()->id, DiscussioniTopic::className(),
                    $search->buildQuery('to-validate', []));
            }
            $this->setBulletCount($count);
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

        //aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
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