<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

namespace lispa\amos\discussioni\widgets\graphics;

use lispa\amos\core\widget\WidgetGraphic;
use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\discussioni\models\search\DiscussioniTopicSearch;

/**
 *
 * @deprecated
 * Class WidgetGraphicsDiscussioniInEvidenza
 * informational widget that lists threads in evidence
 * @package lispa\amos\discussioni\widgets\graphics
 */
class WidgetGraphicsDiscussioniInEvidenza extends WidgetGraphic
{

    public function init()
    {
        parent::init();
        $this->setCode('ULTIME_DISCUSSIONI_GRAPHIC');
        $this->setLabel(AmosDiscussioni::tHtml('amosdiscussioni', 'Discussioni in evidenza'));
        $this->setDescription(AmosDiscussioni::t('amosdiscussioni', 'Elenca le ultime discussioni in evidenza'));
    }

    /**
     * rendering of the view discussioni_in_evidenza
     *
     * @return string
     */
    public function getHtml()
    {
        $listaTopic = (new DiscussioniTopicSearch())->discussioniInEvidenza($_GET, 3);
        return $this->render('discussioni_in_evidenza', [
            'listaTopic' => $listaTopic,
            'widget' => $this,
            'toRefreshSectionId' => 'widgetGraphicStickyThreads'
        ]);
    }

}