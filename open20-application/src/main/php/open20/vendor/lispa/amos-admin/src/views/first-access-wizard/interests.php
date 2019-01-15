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
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\WizardPrevAndContinueButtonWidget;

/**
 * @var \yii\web\View $this
 * @var \lispa\amos\admin\models\UserProfile $model
 */

$moduleCwh = Yii::$app->getModule('cwh');
$moduleTag = Yii::$app->getModule('tag');

?>

<div class="first-access-wizard-interests">
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'first-access-wizard-form',
            'class' => 'form',
            'enableClientValidation' => true,
            'errorSummaryCssClass' => 'error-summary alert alert-error'
        ]
    ]); ?>
    
    <?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in', 'role' => 'alert']); ?>
    <?= $this->render('parts/header', ['model' => $model]) ?>

    <section>
        <div class="row">
            <div class="col-xs-12">
                <h4><?= AmosAdmin::t('amosadmin', '#faw_interest_text_1') ?></h4>
                <h4><?= AmosAdmin::t('amosadmin', '#faw_interest_text_2') ?></h4>
            </div>
            <?php if (isset($moduleCwh) && isset($moduleTag)): ?>
                <?= \lispa\amos\cwh\widgets\TagWidgetAreeInteresse::widget([
                    'model' => $model,
                    'attribute' => 'areeDiInteresse',
                    'form' => \yii\base\Widget::$stack[0]
                ]); ?>
            <?php endif; ?>
        </div>
    </section>
    
    <?= WizardPrevAndContinueButtonWidget::widget([
        'model' => $model,
        'previousUrl' => Yii::$app->getUrlManager()->createUrl(['/admin/first-access-wizard/role-and-area'])
    ]) ?>
    <?php ActiveForm::end(); ?>
</div>
