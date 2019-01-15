<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\first-access-wizard
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\components\FirstAccessWizardParts;
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\WizardPrevAndContinueButtonWidget;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\admin\models\UserProfile $model
 * @var \lispa\amos\admin\models\UserProfile $facilitatorUserProfile
 */

$firstAccessWizardParts = new FirstAccessWizardParts(['model' => $model]);

?>

<div class="first-access-wizard-introduction">
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'first-access-wizard-form',
            'class' => 'form',
            'errorSummaryCssClass' => 'error-summary alert alert-error'
        ]
    ]); ?>
    <?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in', 'role' => 'alert']); ?>
    <section>
        <div class="row">
            <div class="col-xs-12">
                <h2>
                    <?= AmosAdmin::t('amosadmin', '#faw_intro_title', [
                        'appName' => Yii::$app->name,
                        'name' => $model->nome,
                        'lastname' => $model->cognome
                    ]) ?>
                </h2>
            </div>
        </div>
        <hr>
        <div class="col-xs-12 nop">
            <h4>
                <?= AmosAdmin::t('amosadmin', '#faw_intro_text_1', [
                    'appName' => Yii::$app->name,
                ]) ?>
            </h4>
            <h4>
                <?= AmosAdmin::t('amosadmin', '#faw_intro_text_2') ?>
            </h4>
            <h4>
                <?= AmosAdmin::t('amosadmin', '#faw_intro_text_3') ?>
            </h4>
    </section>

    <?= WizardPrevAndContinueButtonWidget::widget([
        'model' => $model,
        'viewPreviousBtn' => false
    ]) ?>
    <?php ActiveForm::end(); ?>
</div>
