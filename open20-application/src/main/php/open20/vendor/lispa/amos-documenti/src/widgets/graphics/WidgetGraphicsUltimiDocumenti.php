<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\widgetRs\graphics
 * @category   CategoryName
 */

namespace lispa\amos\documenti\widgets\graphics;

use lispa\amos\core\widget\WidgetGraphic;
use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\documenti\models\search\DocumentiSearch;
use lispa\amos\notificationmanager\base\NotifyWidgetDoNothing;

class WidgetGraphicsUltimiDocumenti extends WidgetGraphic
{
    /**
     * @inheritdocF
     */
    public function init()
    {
        parent::init();

        $this->setCode('ULTIME_DOCUMENTI_GRAPHIC');
        $this->setLabel(AmosDocumenti::tHtml('amosdocumenti', 'Ultimi documenti'));
        $this->setDescription(AmosDocumenti::t('amosdocumenti', 'Elenca gli ultimi documenti'));
    }

    public function getHtml()
    {
        $search = new DocumentiSearch();
        $search->setNotifier(new NotifyWidgetDoNothing());
        $listaDocumenti = $search->lastDocuments($_GET, 3);

        $moduleL = \Yii::$app->getModule('layout');
        $viewToRender = 'ultimi_documenti';
        if(empty($moduleL)){
            $viewToRender = 'ultimi_documenti_old';
        }

        return $this->render($viewToRender, [
            'listaDocumenti' => $listaDocumenti,
            'widget' => $this,
            'toRefreshSectionId' => 'widgetGraphicLatestDocumenti'
        ]);
    }
}