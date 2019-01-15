<?php

namespace lispa\amos\comuni\widgets\helpers;

use lispa\amos\comuni\assets\ComuniAsset;
use yii\base\Exception;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class AmosComuniWidget
 * @package lispa\amos\comuni\widgets\helpers
 *
 * <p><b>Widget che permette la creazione delle tendine dei dati residenziali: Nazione, Provincia, Comune, Cap</b></p>
 *
 * Ogni tendina è configurabile con le configurazioni previste dai widget usato:
 * - Nazione => Select2
 * - Provincia => Select2
 * - Comune => DepDrop
 * - CAP => DepDrop
 *
 * campo fondamentale 'attribute' dove indicare il nome del field del model da utilizzare
 *
 * <p>esempio di configurazione base</p>
 *
 * ```php
 * echo \lispa\amos\comuni\widgets\helpers\AmosComuniWidget::widget([
 *   'form' => $form,
 *   'model' => $model,
 *   'nazioneConfig' => [
 *       'attribute' => 'nazione_id',
 *   ],
 *   'provinciaConfig' => [
 *       'attribute' => 'istat_province_id',
 *   ],
 *   'comuneConfig' => [
 *       'attribute' => 'istat_comuni_id',
 *   ],
 *   'capConfig' => [
 *       'attribute' => 'cap_id',
 *   ]
 *   ]);
* ```
 */
class AmosComuniWidget extends Widget
{
    public $form;
    public $model;
    public $nazioneConfig;
    public $provinciaConfig;
    public $comuneConfig;
    public $capConfig;
    protected $params;

    public function init()
    {
        parent::init();

        //controllo esistenza attributo form
        if( !isset($this->form)) {
            throw new Exception( \Yii::t('app', 'Undefined Form object'));
        }
        //controllo esistenza attributo model
        if( !isset($this->model)) {
            throw new Exception( \Yii::t('app', 'Undefined Model object'));
        }

        //se presente Provincia è richiesto anche Comune: se presente provincia ma non comune lancio errore
        if(isset($this->provinciaConfig) && !isset($this->comuneConfig)) {
            throw new Exception( \Yii::t('app', 'Provincia e Comune must both be declared'));
        }

        //Cap necessita di Comune
        if( (isset($this->capConfig) && !isset($this->comuneConfig)) ){
            throw new Exception( \Yii::t('app', 'Comune must be declared for Cap usage'));
        }

        $this->params = [
            'model' => $this->model,
            'form' => $this->form,
            'nazioneConfig' => $this->nazioneConfig,
            'provinciaConfig' => $this->provinciaConfig,
            'comuneConfig' => $this->comuneConfig,
            'capConfig' => $this->capConfig
        ];

    }

    public function run()
    {
        //registro il file comuni_common_js.js a tutte le varie view
        ComuniAsset::register(\Yii::$app->getView());

        $html_nazione = '';
        $html_provincia = '';
        $html_comune = '';
        $html_cap = '';

        if(isset($this->nazioneConfig)){
            $html_nazione =  $this->render('nazione_view', $this->params );
        }
        if(isset($this->provinciaConfig)) {
            $html_provincia = $this->render('provincia_view', $this->params);
            $html_comune = $this->render('comune_view', $this->params);
        } else {
            $html_comune = $this->render('comune_single_select_view', $this->params);
        }

        if(isset($this->capConfig)){
            $html_cap =  $this->render('cap_view', $this->params );
        }


        $html_complete = $html_nazione. $html_provincia . $html_comune . $html_cap;
        return $html_complete;
    }
}