<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */

use lispa\amos\emailmanager\AmosEmail;
use lispa\amos\emailmanager\models\EmailTemplate;
use lispa\amos\core\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model EmailTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>

    <div class="row">
        <div class="col-lg-6 col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-6 col-sm-6">
            <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <?= $form->field($model, 'heading')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
        <?= 
              $form->field($model, 'message')->widget(\yii\redactor\widgets\Redactor::className(), [
                    'clientOptions' => [
                        //'placeholder' => AmosEmail::t('amosemail','SOMETHING'),
                        'placeholder' => '',
                        'lang' => 'it',
                         'minHeight' => 300,
                        'buttons' => ['html', 'formatting', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist','outdent', 'indent', 'image', 'link', 'alignment', 'horizontalrule'],
                        'plugins' => ['indent','clips', 'fontcolor','imagemanager', 'fontsize','table', 'lang', 'paragraphize']
                    ]
                ])    

                ?>
        </div>
    </div>
    <div class="col-xs-12 note_asterisk nop">
        <p>I campi <span class="red">*</span> sono obbligatori.</p>
    </div>
    <div class="btnViewContainer pull-right">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
