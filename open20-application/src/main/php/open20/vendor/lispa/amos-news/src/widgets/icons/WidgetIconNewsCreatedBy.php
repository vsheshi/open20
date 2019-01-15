<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\news
 * @category   CategoryName
 */

namespace lispa\amos\news\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\news\AmosNews;
use lispa\amos\news\models\search\NewsSearch;
use yii\helpers\ArrayHelper;
use Yii;
use \lispa\amos\news\models\News;

/**
 * Class WidgetIconNewsCreatedBy
 * @package lispa\amos\news\widgets\icons
 */
class WidgetIconNewsCreatedBy extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $options = parent::getOptions();

        // Aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
        return ArrayHelper::merge($options, ["children" => []]);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(AmosNews::tHtml('amosnews', 'Notizie create da me'));
        $this->setDescription(AmosNews::t('amosnews', 'Visualizza le notizie create da me'));
        $this->setIcon('feed');
        //$this->setIconFramework();
        $this->setUrl(['/news/news/own-news']);
        $search = new NewsSearch();
        $notifier = Yii::$app->getModule('notify');
        $count = 0;
        if($notifier)
        {
            $count = $notifier->countNotRead(Yii::$app->getUser()->id, News::class,
                $search->buildQuery([], 'created-by'));
        }
        $this->setBulletCount($count);
        
        $this->setCode('NEWS_CREATEDBY');
        $this->setModuleName('news');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }
    
}
