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
use yii\helpers\ArrayHelper;

class WidgetIconDocumentiCategorie extends WidgetIcon
{
    public function getOptions()
    {
        $options = parent::getOptions();

        //aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
        return ArrayHelper::merge($options, ["children" => []]);
    }

    public function init()
    {
        parent::init();

        $this->setLabel(AmosDocumenti::tHtml('amosdocumenti', 'Categorie documenti'));
        $this->setDescription(AmosDocumenti::t('amosdocumenti', 'Visualizza le categorie dei documenti'));

        $this->setIcon('file-text-o');
        //$this->setIconFramework();

        $this->setUrl(['/documenti/documenti-categorie/index']);

        $this->setCode('DOCUMENTI_CATEGORIE');
        $this->setModuleName('documenti');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }
}