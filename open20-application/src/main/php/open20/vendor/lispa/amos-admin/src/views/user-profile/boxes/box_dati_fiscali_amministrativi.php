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
use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;

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
        <?= AmosIcons::show('case'); ?>
        <?= AmosAdmin::tHtml('amosadmin', 'Dati Fiscali e Amministrativi') ?>
    </h2>
    <!--
    <div class="bk-testoBoxInfo">
        <p>< ?= AmosAdmin::tHtml('amosadmin', "I dati amministrativi consentono la fatturazione e il pagamento delle parcelle, assicurarsi che i dati inseriti siano corretti."); ?></p>
    </div>-->

    <div class="row">
        <?php if ($adminModule->confManager->isVisibleField('codice_fiscale', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($model, 'codice_fiscale')->textInput(['data-message' => Html::error($model, 'codice_fiscale')]) ?>
                <?= Html::error($model, 'codice_fiscale'); ?>
            </div>
        <?php endif; ?>
        <!--        <div class="col-lg-4 col-sm-4">
            < ?= $form->field($model, 'partita_iva')->textInput(['readonly' => false]) ?>
        </div>
        <div class="col-lg-4 col-sm-4">
            < ?= $form->field($model, 'iban')->textInput(['readonly' => false]) ?>
        </div>-->
    </div>
</section>
