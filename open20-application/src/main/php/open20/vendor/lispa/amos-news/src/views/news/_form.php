<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\news
 * @category   CategoryName
 */

use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use lispa\amos\core\forms\CreatedUpdatedWidget;
use lispa\amos\core\forms\editors\Select;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\forms\WorkflowTransitionWidget;
use lispa\amos\news\AmosNews;
use lispa\amos\news\models\News;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var lispa\amos\news\models\News $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<?php
\lispa\amos\layout\assets\SpinnerWaitAsset::register($this);

$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'], // important
	'id' => 'news-form'
]);
$customView = Yii::$app->getViewPath() . '/imageField.php';
$this->registerCss(".addNew button { color:white;}");

$js = <<<JS
$('body').on('beforeValidate', '#news-form', function (e) {
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
  }).on('afterValidate', '#news-form', function (e) {
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
$errorSummary = $form->errorSummary($model);
echo $errorSummary;
?>

<div class="loading" hidden></div>
<?= WorkflowTransitionWidget::widget([
    'form' => $form,
    'model' => $model,
    'workflowId' => News::NEWS_WORKFLOW,
    'classDivIcon' => 'pull-left',
    'classDivMessage' => 'pull-left message',
    'viewWidgetOnNewRecord' => true
]); ?>
<div class="news-form col-xs-12 nop">

    <?php $this->beginBlock('dettagli'); ?>

    <div class="row">
        <div class="col-lg-8 col-sm-8">
            <?= $form->field($model, 'titolo')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'sottotitolo')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'descrizione_breve')->textarea(['maxlength' => true, 'rows' => 3]) ?>
        </div>
        <div class="col-lg-4 col-sm-4">
            <div class="col-xs-12 pull-right">
                <?= $form->field($model,
                    'newsImage')->widget(\lispa\amos\attachments\components\AttachmentsInput::classname(), [
                    'options' => [ // Options of the Kartik's FileInput widget
                        'multiple' => false, // If you want to allow multiple upload, default to false
                        'accept' => "image/*"
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
                ])->label(AmosNews::t('amosnews', 'Immagine della notizia')) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <?= $form->field($model, 'descrizione')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'placeholder' => AmosNews::t('amosnews',
                        'Dopo aver compilato tutti i campi obbligatori clicca su "Salva" per inserire la notizia'),
                    'buttonsHide' => [
                        'image',
                        'file'
                    ],
                    'lang' => substr(Yii::$app->language, 0, 2)
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-sm-4">
            <?php
                $append = '';
                if (\Yii::$app->getUser()->can('NEWSCATEGORIE_CREATE')) {
                    $append = ' canInsert';
                }
                ?>
            <?= $form->field($model, 'news_categorie_id')->widget(Select::className(), [
                'auto_fill' => true,
                'options' => [
                    'placeholder' => AmosNews::t('amosnews', 'Digita il nome della categoria'),
                    'id' => 'news_categorie_id-id',
                    'disabled' => false,
                    'class' => 'dynamicCreation' . $append,
                    'data-model' => 'news_categorie',
                    'data-field' => 'titolo',
                    'data-module' => 'news',
                    'data-entity' => 'news-categorie',
                    'data-toggle' => 'tooltip',
                ],
                'pluginEvents' => [
                    "select2:open" => "dynamicInsertOpening"
                ],
                'data' =>
                    ArrayHelper::map(\lispa\amos\news\utility\NewsUtility::getNewsCategories()
                        ->orderBy('titolo')->all(),
                        'id', 'titolo')

            ]); ?>


        </div>
        <?php
        if (Yii::$app->getModule('news')->params['site_publish_enabled']) { ?>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'primo_piano')->widget(Select::className(), [
                    'auto_fill' => true,
                    'data' => [
                        '0' => AmosNews::t('amosnews', 'No'),
                        '1' => AmosNews::t('amosnews', 'Si')
                    ],
                    'options' => [
                        'prompt' => AmosNews::t('amosnews', 'Seleziona'),
                        'disabled' => false,
                        'onchange' => "
                        if($(this).val() == 1) $('#news-in_evidenza').prop('disabled', false);
                        if($(this).val() == 0) {
                            $('#news-in_evidenza').prop('disabled', true);
                            $('#news-in_evidenza').val(0);
                        }"
                    ],
                ]) ?>
            </div>
        <?php }
        if (Yii::$app->getModule('news')->params['site_featured_enabled']) { ?>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'in_evidenza')->widget(Select::className(), [
                    'auto_fill' => true,
                    'data' => [
                        '0' => AmosNews::t('amosnews', 'No'),
                        '1' => AmosNews::t('amosnews', 'Si')
                    ],
                    'options' => [
                        'prompt' => AmosNews::t('amosnews', 'Seleziona'),
                        'disabled' => ($model->primo_piano == 1 ? false : true)
                    ]
                ]) ?>
            </div>
        <?php } ?>
    </div>

    <div class="row">
        <?php
        $moduleNews = \Yii::$app->getModule(AmosNews::getModuleName());
        if ($moduleNews->hidePubblicationDate == false) {
            ?>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'data_pubblicazione')->widget(DateControl::className(), [
                    'type' => DateControl::FORMAT_DATE
                ]) ?>
            </div>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'data_rimozione')->widget(DateControl::className(), [
                    'type' => DateControl::FORMAT_DATE
                ]) ?>
            </div>
        <?php } ?>
        <div class="col-lg-4 col-sm-4">
            <?= $form->field($model, 'comments_enabled')->widget(Select::className(), [
                'auto_fill' => true,
                'data' => [
                    '0' => AmosNews::t('amosnews', 'No'),
                    '1' => AmosNews::t('amosnews', 'Si')
                ],
                'options' => [
                    'prompt' => AmosNews::t('amosnews', 'Seleziona'),
                    'disabled' => false
                ]
            ]) ?>
        </div>
    </div>

    <?php $this->endBlock(); ?>

    <?php
    $itemsTab[] = [
        'label' => AmosNews::t('amosnews', 'Dettagli '),
        'content' => $this->blocks['dettagli'],
        'options' => ['id' => 'tab-dettagli'],
    ];
    ?>

    <?php $this->beginBlock('allegati'); ?>
    <?= $form->field($model,
        'attachments')->widget(\lispa\amos\attachments\components\AttachmentsInput::classname(), [
        'options' => [ // Options of the Kartik's FileInput widget
            'multiple' => true, // If you want to allow multiple upload, default to false
        ],
        'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
            'maxFileCount' => 100,
            'showPreview' => false// Client max files
        ]
    ])->label(AmosNews::t('amosnews', 'Allegati')) ?>
    <?= \lispa\amos\attachments\components\AttachmentsTableWithPreview::widget([
        'model' => $model,
        'attribute' => 'attachments'
    ]) ?>

    <div class="clearfix"></div>
    <?php $this->endBlock(); ?>

    <?php
    $itemsTab[] = [
        'label' => AmosNews::t('amosnews', 'Allegati'),
        'content' => $this->blocks['allegati'],
        'options' => ['id' => 'allegati'],
    ];
    ?>

    <?php
    $hideReport = false;
    if(!$model->isNewRecord && (\Yii::$app->user->id != $model->created_by) && !\lispa\amos\community\utilities\CommunityUtil::isLoggedCommunityManager()){
        $hideReport = true;
    }?>
    <?= Tabs::widget(
        [
            'encodeLabels' => false,
            'items' => $itemsTab,
            'hideReportTab' => $hideReport
        ]
    ); ?>

    <?php
    $config = [
        'model' => $model,
        'urlClose' => Yii::$app->session->get('previousUrl'),
        'buttonNewSaveLabel' => (\Yii::t('amoscore', '#save_and_close')),
        'buttonSaveLabel' => \Yii::t('amoscore', '#save_and_close')

    ];
    ?>
    <div class="col-xs-12 note_asterisk nop">
        <p><?= AmosNews::t('amosnews', 'I campi') ?> <span
                    class="red">*</span> <?= AmosNews::t('amosnews', 'sono obbligatori') ?>.</p>
    </div>
    <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>
    <?= CloseSaveButtonWidget::widget($config); ?>
</div>
<?php ActiveForm::end(); ?>
