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
 * Class WidgetIconDiscussioniTopic
 * This widget can appear on dashboard. This class is used for creation and general configuration.
 * Widget that link to the discussion topic
 *
 * @package lispa\amos\discussioni\widgets\icons
 */
class WidgetIconDiscussioniTopic extends WidgetIcon
{
    /**
     * Init of the class, set of general configurations
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosDiscussioni::tHtml('amosdiscussioni', 'Discussioni di mio interesse'));
        $this->setDescription(AmosDiscussioni::t('amosdiscussioni', 'Elenco discussioni di mio interesse'));
        $this->setIcon('comment');
        $this->setUrl(['/discussioni/discussioni-topic/own-interest-discussions']);
        $this->setCode('DISCUSSIONI_DI_MIO_INTERESSE');
        $this->setModuleName('discussioni');
        $this->setNamespace(__CLASS__);

        if (Yii::$app instanceof Web) {
            $search = new DiscussioniTopicSearch();
            $notifier = \Yii::$app->getModule('notify');
            $count = 0;
            if ($notifier) {
                $count = $notifier->countNotRead(\Yii::$app->getUser()->id, DiscussioniTopic::className(),
                    $search->buildQuery('own-interest', []));
            }
            $this->setBulletCount($count);
        }

        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }
}
