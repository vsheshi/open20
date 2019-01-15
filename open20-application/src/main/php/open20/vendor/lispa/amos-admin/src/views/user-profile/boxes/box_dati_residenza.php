<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile\boxes
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\comuni\models\IstatNazioni;
use lispa\amos\comuni\models\IstatProvince;
use lispa\amos\core\icons\AmosIcons;
use kartik\depdrop\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

?>
<section class="section-data">
    <h2>
        <?= AmosIcons::show('home') ?>
        <?= AmosAdmin::tHtml('amosadmin', 'Dati di Residenza'); ?>
    </h2>

    <div class="row">
        <div class="col-lg-4 col-sm-4">
            <div class="select">
                <?=
                $form->field($model, 'residenza_nazione_id')->widget(Select2::classname(), [
                    'options' => ['placeholder' => AmosAdmin::t('amosadmin', 'Digita il nome della nazione'), 'id' => 'residenza_nazione_id', 'disabled' => false],
                    'data' => ArrayHelper::map(IstatNazioni::find()->orderBy('nome')->asArray()->all(), 'id', 'nome')
                ]);
                ?>
            </div>
        </div>
        <div class="col-lg-4 col-sm-4">
            <div class="select">
                <?=
                $form->field($model, 'provincia_residenza_id')->widget(Select2::classname(), [
                    'options' => ['placeholder' => AmosAdmin::t('amosadmin', 'Digita il nome della provincia'), 'id' => 'provincia_residenza_id-id', 'disabled' => false],
                    'data' => ArrayHelper::map(IstatProvince::find()->orderBy('nome')->asArray()->all(), 'id', 'nome')
                ]);
                ?>
            </div>
        </div>
        <div class="col-lg-4 col-sm-4">
            <div class="select">
                <?=
                $form->field($model, 'comune_residenza_id')->widget(DepDrop::classname(), [
                    'type' => DepDrop::TYPE_SELECT2,
                    'data' => $model->residenzaComune ? [$model->residenzaComune->id => $model->residenzaComune->nome] : [],
                    'options' => ['id' => 'comune_residenza_id-id', 'disabled' => false],
                    'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                    'pluginOptions' => [
                        'depends' => [(false) ?: 'provincia_residenza_id-id'],
                        'placeholder' => [AmosAdmin::t('amosadmin', 'Seleziona ...')],
                        'url' => Url::to(['/comuni/default/comuni-by-provincia', 'soppresso' => 0]),
                        'initialize' => true,
                        'params' => ['comune_residenza_id-id'],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7 col-sm-7">
            <?= $form->field($model, 'indirizzo_residenza')->textInput(['maxlength' => 255, 'title' => AmosAdmin::t('amosadmin', 'In questo campo inserisci l\'indirizzo, il numero civico va inserito nel campo successivo, sulla destra'), 'readonly' => false]) ?>
        </div>
        <div class="col-lg-5 col-sm-5">
            <div class="col-lg-5 col-sm-5">
                <?= $form->field($model, 'numero_civico_residenza')->textInput(['maxlength' => 10, 'title' => AmosAdmin::t('amosadmin', 'Inserisci il civico'), 'readonly' => false]) ?>
            </div>
            <div class="col-lg-7 col-sm-7">
                <?= $form->field($model, 'cap_residenza')->textInput(['title' => AmosAdmin::tHtml('amosadmin', 'Inserisci il CAP'), 'readonly' => false]) ?>
            </div>
        </div>
    </div>
</section>
