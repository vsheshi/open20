<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti
 * @category   CategoryName
 */

namespace lispa\amos\documenti\widgets\icons;


use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\documenti\AmosDocumenti;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Application as Web;
use lispa\amos\dashboard\models\AmosUserDashboards;


class WidgetIconDocumentiDashboard extends WidgetIcon
{
    public function init()
    {
        parent::init();

        $this->setLabel(AmosDocumenti::tHtml('amosdocumenti', 'Documenti'));
        $this->setDescription(AmosDocumenti::t('amosdocumenti', 'Modulo documenti'));

        $this->setIcon('file-text-o');
        //$this->setIconFramework();
        
        $this->setUrl(['/documenti']);

        $this->setCode('DOCUMENTI_MODULE');
        $this->setModuleName('documenti-dashboard');
        $this->setNamespace(__CLASS__);
        if (Yii::$app instanceof Web) {
            $this->setBulletCount($this->getBulletCountChildWidgets());
        }
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));

    }

    public function getOptions()
    {
        $options = parent::getOptions();

        //aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
        return ArrayHelper::merge($options, ["children" => $this->getWidgetsIcon()]);
    }

    /* TEMPORANEA */
    public function getWidgetsIcon()
    {
        $widgets = [];

        $WidgetIconDocumentiCategorie = new WidgetIconDocumentiCategorie();
        if ($WidgetIconDocumentiCategorie->isVisible()) {
            $widgets[] = $WidgetIconDocumentiCategorie->getOptions();
        }

        $WidgetIconDocumentiCreatedBy = new WidgetIconDocumentiCreatedBy();
        if ($WidgetIconDocumentiCreatedBy->isVisible()) {
            $widgets[] = $WidgetIconDocumentiCreatedBy->getOptions();
        }

        return $widgets;
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
                'module' => AmosDocumenti::getModuleName()
            ]);
            if (is_null($userModuleDashboard)) return 0;

            $widget = \Yii::createObject(WidgetIconAllDocumenti::className());
            $count = $widget->getBulletCount();
        }catch(Exception $ex){
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $count;
    }
}