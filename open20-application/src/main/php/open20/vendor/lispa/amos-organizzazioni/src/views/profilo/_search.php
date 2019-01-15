<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

use lispa\amos\core\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/**
 *
 * @var yii\web\View $this
 * @var lispa\amos\organizzazioni\models\search\ProfiloSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>
<div class="are-profilo-search element-to-toggle" data-toggle-element="form-search">

    <?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
		'options' => [
			'class' => 'default-form'
		]
	]);
?>

    <!-- name -->
<div class="col-md-4"> <?php echo
$form->field($model, 'name')->textInput(['placeholder' => 'ricerca per denominazione' ]) ?>

 </div>

<!-- partita_iva -->
<div class="col-md-4"> <?php echo
$form->field($model, 'partita_iva')->textInput(['placeholder' => 'ricerca per partita iva' ]) ?>

 </div>

<!-- codice_fiscale -->
<div class="col-md-4"> <?php echo
$form->field($model, 'codice_fiscale')->textInput(['placeholder' => 'ricerca per codice fiscale' ]) ?>

 </div>

<!-- presentazione_della_organizzaz -->
<div class="col-md-4"> <?php echo
$form->field($model, 'presentazione_della_organizzaz')->textInput(['placeholder' => 'ricerca per presentazione della organizzaz' ]) ?>

 </div>


<!-- tipologia_di_organizzazione -->
<div class="col-md-4"> <?php echo
$form->field($model, 'tipologia_di_organizzazione')->textInput(['placeholder' => 'ricerca per tipologia di organizzazione' ]) ?>

 </div>

<!-- forma_legale -->
<div class="col-md-4"> <?php echo
$form->field($model, 'forma_legale')->textInput(['placeholder' => 'ricerca per forma legale' ]) ?>

 </div>

<!-- sito_web -->
<div class="col-md-4"> <?php echo
$form->field($model, 'sito_web')->textInput(['placeholder' => 'ricerca per sito web' ]) ?>

 </div>

<!-- facebook -->
<div class="col-md-4"> <?php echo
$form->field($model, 'facebook')->textInput(['placeholder' => 'ricerca per facebook' ]) ?>

 </div>

<!-- twitter -->
<div class="col-md-4"> <?php echo
$form->field($model, 'twitter')->textInput(['placeholder' => 'ricerca per twitter' ]) ?>

 </div>

<!-- linkedin -->
<div class="col-md-4"> <?php echo
$form->field($model, 'linkedin')->textInput(['placeholder' => 'ricerca per linkedin' ]) ?>

 </div>

<!-- google -->
<div class="col-md-4"> <?php echo
$form->field($model, 'google')->textInput(['placeholder' => 'ricerca per google' ]) ?>

 </div>

<!-- indirizzo -->
<div class="col-md-4"> <?php echo
$form->field($model, 'indirizzo')->textInput(['placeholder' => 'ricerca per indirizzo' ]) ?>

 </div>

<!-- telefono -->
<div class="col-md-4"> <?php echo
$form->field($model, 'telefono')->textInput(['placeholder' => 'ricerca per telefono' ]) ?>

 </div>

<!-- fax -->
<div class="col-md-4"> <?php echo
$form->field($model, 'fax')->textInput(['placeholder' => 'ricerca per fax' ]) ?>

 </div>

<!-- email -->
<div class="col-md-4"> <?php echo
$form->field($model, 'email')->textInput(['placeholder' => 'ricerca per email' ]) ?>

 </div>

<!-- pec -->
<div class="col-md-4"> <?php echo
$form->field($model, 'pec')->textInput(['placeholder' => 'ricerca per pec' ]) ?>

 </div>

<!-- la_sede_legale_e_la_stessa_del -->
<div class="col-md-4"> <?php echo
$form->field($model, 'la_sede_legale_e_la_stessa_del')->textInput(['placeholder' => 'ricerca per la sede legale e la stessa del' ]) ?>

 </div>

<!-- sede_legale_indirizzo -->
<div class="col-md-4"> <?php echo
$form->field($model, 'sede_legale_indirizzo')->textInput(['placeholder' => 'ricerca per sede legale indirizzo' ]) ?>

 </div>

<!-- sede_legale_telefono -->
<div class="col-md-4"> <?php echo
$form->field($model, 'sede_legale_telefono')->textInput(['placeholder' => 'ricerca per sede legale telefono' ]) ?>

 </div>

<!-- sede_legale_fax -->
<div class="col-md-4"> <?php echo
$form->field($model, 'sede_legale_fax')->textInput(['placeholder' => 'ricerca per sede legale fax' ]) ?>

 </div>

<!-- sede_legale_email -->
<div class="col-md-4"> <?php echo
$form->field($model, 'sede_legale_email')->textInput(['placeholder' => 'ricerca per sede legale email' ]) ?>

 </div>

<!-- sede_legale_pec -->
<div class="col-md-4"> <?php echo
$form->field($model, 'sede_legale_pec')->textInput(['placeholder' => 'ricerca per sede legale pec' ]) ?>

 </div>

<!-- responsabile -->
<div class="col-md-4"> <?php echo
$form->field($model, 'responsabile')->textInput(['placeholder' => 'ricerca per responsabile' ]) ?>

 </div>

<!-- rappresentante_legale -->
<div class="col-md-4"> <?php echo
$form->field($model, 'rappresentante_legale')->textInput(['placeholder' => 'ricerca per rappresentante legale' ]) ?>

 </div>

    <div class="col-xs-12">
        <div class="pull-right">
            <?php echo Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
            <?php echo Html::submitButton('Search', ['class' => 'btn btn-navigation-primary']) ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <!--a><p class="text-center">Ricerca avanzata<br>
            < ?=AmosIcons::show('caret-down-circle');?>
        </p></a-->
    <?php ActiveForm::end(); ?>
</div>
