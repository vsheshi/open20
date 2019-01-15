<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use lispa\amos\core\forms\CreatedUpdatedWidget;
use lispa\amos\core\forms\Tabs;
use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\discussioni\models\DiscussioniTopic;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var lispa\amos\discussioni\models\DiscussioniTopic $model
 * @var yii\widgets\ActiveForm $form
 */
\lispa\amos\layout\assets\SpinnerWaitAsset::register($this);

 $js = <<<JS
$('body').on('beforeValidate', '#discussioni-form', function (e) {
  $(':input[type="submit"]', this).attr('disabled', 'disabled');
  $(':input[type="submit"]', this).each(function (i) {
	if ($(this).prop('tagName').toLowerCase() === 'input') {
      $(this).data('enabled-text', $(this).val());
      $(this).val($(this).data('disabled-text'));
    } else {
      $(this).data('enabled-text', $(this).html());
      $(this).html($(this).data('disabled-text'));
    }
  });
  }).on('afterValidate', '#discussioni-form', function (e) {
  if ($(this).find('.has-error').length > 0) {
    $(':input[type="submit"]', this).removeAttr('disabled');
    $(':input[type="submit"]', this).each(function (i) {
      if ($(this).prop('tagName').toLowerCase() === 'input') {
        $(this).val($(this).data('enabled-text'));
      } else {
        $(this).html($(this).data('enabled-text'));
      }
    });
  } else {
     $('.loading').show();
  }
});
JS;

$this->registerJs($js, View::POS_READY);
$this->registerCss(".error-summary { padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    background-color: #f2dede;
    border-color: #ebccd1;}");
?>

<?php
$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'], // important
	'id' => 'discussioni-form'
]);
$errorSummary = $form->errorSummary($model);

echo $errorSummary;
$customView = Yii::$app->getViewPath() . '/imageField.php';
$utenteConnesso = Yii::$app->getUser();
?>

<?= \lispa\amos\core\forms\WorkflowTransitionWidget::widget([
    'form' => $form,
    'model' => $model,
    'workflowId' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
    'classDivIcon' => 'pull-left',
    'classDivMessage' => 'pull-left message',
    'viewWidgetOnNewRecord' => true
]); ?>

<div class="loading" hidden></div>
<div class="discussioni-form col-xs-12">
    
    <?php $this->beginBlock('dettagli'); ?>
    <div class="row">
        <div class="col-lg-8 col-sm-8">
            <?= $form->field($model, 'titolo')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-4 col-sm-4">

            <div class="col-lg-8 col-sm-8 pull-right">
                <?=
                $form->field($model,
                    'discussionsTopicImage')->widget(\lispa\amos\attachments\components\AttachmentsInput::classname(), [
                    'options' => [
                        'multiple' => FALSE,
                        'accept' => "image/*",
                    ],
                    'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                        'maxFileCount' => 1,
                        'showRemove' => false,// Client max files,
                        'indicatorNew' => false,
                        'allowedPreviewTypes' => ['image'],
                        'previewFileIconSettings' => false,
                        'overwriteInitial' => false,
                        'layoutTemplates' => false
                    ]
                ])->label(AmosDiscussioni::t('amosdiscussioni', 'Immagine della discussione'))
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <?php // echo $form->field($model, 'testo')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'testo')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'placeholder' => AmosDiscussioni::t('amosdiscussioni', '#discussions_text_placeholder'),
                    'buttonsHide' => [
                        'image',
                        'file'
                    ],
                    'lang' => substr(Yii::$app->language, 0, 2)
                ]
            ]) ?>
        </div>
    </div>
    <div class="row hidden">
        <div class="col-lg-12 col-sm-12">
            <?= $form->field($model, 'in_evidenza')->checkbox() ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <?php $this->endBlock('dettagli'); ?>
    
    <?php
    $itemsTab[] = [
        'label' => AmosDiscussioni::t('amosdiscussioni', 'Dettagli'),
        'content' => $this->blocks['dettagli'],
    ];
    ?>
    
    <?php $this->beginBlock('allegati'); ?>
    <?= $form->field($model,
        'discussionsAttachments')->widget(\lispa\amos\attachments\components\AttachmentsInput::classname(), [
        'options' => [ // Options of the Kartik's FileInput widget
            'multiple' => true, // If you want to allow multiple upload, default to false
        ],
        'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
            'maxFileCount' => 100,// Client max files,
            'showPreview' => false
        ]
    ])->label(AmosDiscussioni::t('amosdiscussioni', 'Allegati')) ?>
    <?= \lispa\amos\attachments\components\AttachmentsTableWithPreview::widget([
        'model' => $model,
        'attribute' => 'discussionsAttachments'
    ]) ?>

    <div class="clearfix"></div>
    <?php
    $this->endBlock('allegati');
    $itemsTab[] = [
        'label' => AmosDiscussioni::t('amosdiscussioni', 'Allegati '),
        'content' => $this->blocks['allegati'],
        'options' => ['id' => 'allegati'],
    ];
    ?>

    <?php
    $hideReport = false;
    if(!$model->isNewRecord && (\Yii::$app->user->id != $model->created_by) && !\lispa\amos\community\utilities\CommunityUtil::isLoggedCommunityManager()){
        $hideReport = true;
    }?>
    <?=
    Tabs::widget(
        [
            'encodeLabels' => false,
            'items' => $itemsTab,
            'hideReportTab' => $hideReport
        ]
    );
    ?>
    <div class="col-xs-12 note_asterisk nop">
        <p>I campi <span class="red">*</span> sono obbligatori.</p>
    </div>


    <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>
    <?php
        $config = [
            'model' => $model,
            'urlClose' => Yii::$app->session->get('previousUrl'),
            'buttonNewSaveLabel' => (\Yii::t('amoscore', '#save_and_close')),
            'buttonSaveLabel' => \Yii::t('amoscore', '#save_and_close')
        ];
        echo CloseSaveButtonWidget::widget($config);
    ?>
    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>
</div>
