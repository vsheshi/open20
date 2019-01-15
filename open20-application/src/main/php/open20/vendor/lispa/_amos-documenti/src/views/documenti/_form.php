<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\views\documenti
 * @category   CategoryName
 */

use lispa\amos\attachments\components\AttachmentsInput;
use lispa\amos\attachments\components\AttachmentsTableWithPreview;
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use lispa\amos\core\forms\CreatedUpdatedWidget;
use lispa\amos\core\forms\editors\Select;
use lispa\amos\core\forms\RequiredFieldsTipWidget;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\forms\WorkflowTransitionWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\documenti\models\Documenti;
use lispa\amos\documenti\models\DocumentiCategorie;
use kartik\alert\Alert;
use kartik\datecontrol\DateControl;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\redactor\widgets\Redactor;

/**
 * @var yii\web\View $this
 * @var lispa\amos\documenti\models\Documenti $model
 * @var yii\widgets\ActiveForm $form
 */

/** @var \lispa\amos\documenti\controllers\DocumentiController $appController */
$appController = Yii::$app->controller;
$isFolder = $appController->documentIsFolder($model);
$enableCategories = AmosDocumenti::instance()->enableCategories;


?>

<?php
$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'] // important
]);
$customView = Yii::$app->getViewPath() . '/imageField.php';
?>

<?= WorkflowTransitionWidget::widget([
    'form' => $form,
    'model' => $model,
    'workflowId' => Documenti::DOCUMENTI_WORKFLOW,
    'classDivIcon' => 'pull-left',
    'classDivMessage' => 'pull-left message',
    'viewWidgetOnNewRecord' => true
]); ?>
<div class="documenti-form col-xs-12">

    <?php $this->beginBlock('dettagli'); ?>
    <div class="row">
        <div class="col-lg-8 col-sm-8">
            <?= $form->field($model, 'titolo')->textInput(['maxlength' => true]) ?>
            <?php if (!$isFolder): ?>
                <?= $form->field($model, 'sottotitolo')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'descrizione_breve')->textarea(['maxlength' => true, 'rows' => 3]) ?>
            <?php endif; ?>
        </div>
        <div class="col-lg-4 col-sm-4">
            <?php if (!$isFolder): ?>
                <div class="col-lg-12 col-sm-12 pull-left">
                    <?= $form->field($model,
                        'documentMainFile')->widget(AttachmentsInput::classname(), [
                        'options' => [
                            'multiple' => FALSE,
                        ],
                        'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                            'maxFileCount' => 1,
                            'showRemove' => false,// Client max files,
                            'indicatorNew' => false,
                            'allowedPreviewTypes' => false,
                            'previewFileIconSettings' => false,
                            'overwriteInitial' => false,
                            'layoutTemplates' => false,
                        ]
                    ])->label(AmosDocumenti::t('amosdocumenti', 'Documento principale')) ?>

                    <?php if (!empty($documento)): ?>
                        <?= $documento->filename ?>
                        <?= Html::a(AmosIcons::show('download', ['class' => 'btn btn-tool-secondary']), ['/documenti/documenti/download-documento-principale', 'id' => $model->id], [
                            'title' => 'Download file',
                            'class' => 'bk-btnImport'
                        ]); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$isFolder): ?>
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <?= $form->field($model, 'descrizione')->widget(Redactor::className(), [
                    'clientOptions' => [
                        'placeholder' => AmosDocumenti::t('amosdocumenti', '#documents_text_placeholder'),
                        'buttonsHide' => [
                            'image',
                            'file'
                        ],
                        'lang' => substr(Yii::$app->language, 0, 2)
                    ]
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if (!$isFolder && $enableCategories): ?>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'documenti_categorie_id')->widget(Select2::className(), [
                    'options' => ['placeholder' => AmosDocumenti::t('amosdocumenti', 'Digita il nome della categoria'), 'id' => 'documenti_categorie_id-id', 'disabled' => FALSE],
                    'data' => ArrayHelper::map(DocumentiCategorie::find()->orderBy('titolo')->all(), 'id', 'titolo')
                ]); ?>
            </div>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'version')->hiddenInput() ?>
                <div class="m-l-5"><?= ($model->version ? $model->version : '-') ?></div>
            </div>
        <?php endif; ?>
        <?php if (Yii::$app->getModule('documenti')->params['site_publish_enabled']): ?>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'primo_piano')->dropDownList([
                    '0' => 'No',
                    '1' => 'Si'
                ], [
                    'prompt' => AmosDocumenti::t('amosdocumenti', 'Seleziona...'),
                    'disabled' => false,
                    'onchange' => '
                        if($(this).val() == 1) $(\'#documenti-in_evidenza\').prop(\'disabled\', false);
                        if($(this).val() == 0) { 
                            $(\'#documenti-in_evidenza\').prop(\'disabled\', true);
                            $(\'#documenti-in_evidenza\').val(0);
                        }
                        '
                ])
                ?>
            </div>
        <?php endif; ?>
        <?php if (Yii::$app->getModule('documenti')->params['site_featured_enabled']): ?>
            <div class="col-lg-4 col-sm-4">
                <?=
                $form->field($model, 'in_evidenza')->dropDownList([
                    '0' => 'No',
                    '1' => 'Si'
                ], [
                    'prompt' => AmosDocumenti::t('amosdocumenti', 'Seleziona...'),
                    'disabled' => ($model->primo_piano == 0) ? true : false
                ])
                ?>
            </div>
        <?php endif; ?>
        <?php /* $form->field($model, 'abilita_pubblicazione')->dropDownList([
          '0' => 'No',
          '1' => 'Si'
          ], ['prompt' => AmosDocumenti::t('amosdocumenti', 'Seleziona')]
          ) */
        ?>
    </div>

    <div class="row">
        <?php
        $module = \Yii::$app->getModule(AmosDocumenti::getModuleName());
        if($module->hidePubblicationDate == false) { ?>
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
        <?php }?>
        <?php if (!$isFolder): ?>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'comments_enabled')->widget(Select::className(), [
                    'auto_fill' => true,
                    'data' => [
                        '0' => AmosDocumenti::t('amosdocumenti', 'No'),
                        '1' => AmosDocumenti::t('amosdocumenti', 'Si')
                    ],
                    'options' => [
                        'prompt' => AmosDocumenti::t('amosdocumenti', 'Seleziona'),
                        'disabled' => false
                    ]
                ]) ?>
            </div>
        <?php endif; ?>
    </div>
    <?php $this->endBlock(); ?>

    <?php
    $itemsTab[] = [
        'label' => AmosDocumenti::tHtml('amosdocumenti', 'Dettagli '),
        'content' => $this->blocks['dettagli'],
        'options' => ['id' => 'tab-dettagli'],
    ];
    ?>

    <?php if (!$isFolder): ?>
        <?php $this->beginBlock('allegati'); ?>
        <?php if ($model->isNewRecord): ?>
            <?= Alert::widget([
                'type' => Alert::TYPE_WARNING,
                'body' => AmosDocumenti::tHtml('amosdocumenti', 'Prima di poter inserire degli allegati &egrave; necessario salvare il documento.'),
            ]); ?>
        <?php else: ?>
            <?= $form->field($model,
                'documentAttachments')->widget(AttachmentsInput::classname(), [
                'options' => [ // Options of the Kartik's FileInput widget
                    'multiple' => true, // If you want to allow multiple upload, default to false
                ],
                'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                    'maxFileCount' => 100,// Client max files,
                    'showPreview' => false
                ]
            ])->label(AmosDocumenti::t('amosdocumenti', 'Allegati')) ?>
            <?= AttachmentsTableWithPreview::widget([
                'model' => $model,
                'attribute' => 'documentAttachments'
            ]); ?>
        <?php endif; ?>

        <div class="clearfix"></div>
        <?php $this->endBlock(); ?>

        <?php
        $itemsTab[] = [
            'label' => AmosDocumenti::tHtml('amosdocumenti', 'Allegati'),
            'content' => $this->blocks['allegati'],
            'options' => ['id' => 'allegati'],
        ];
        ?>
    <?php endif; ?>

    <?php
    $tabs = [
        'encodeLabels' => false,
        'items' => $itemsTab
    ];
    if($isFolder) {
        $tabs['hideReportTab'] = true;

        $scope = \lispa\amos\cwh\AmosCwh::getInstance()->getCwhScope();
        $scopeFilter = (empty($scope))? false : true;
        if($scopeFilter) {
            $tabs['hideCwhTab'] = true;
            $idScope = null;
            echo"<div style='display:none'>";
            foreach ($scope as $key => $id) {
                $idScope = $id;
                echo $form->field($model, 'destinatari[]')->hiddenInput(['value' => $key.'-'.$idScope, 'id' => 'cwh-destinatari'])->label(false);
            }
            echo $form->field($model, 'regola_pubblicazione')->hiddenInput(['value' => 3, 'id' => 'cwh-regola_pubblicazione'])->label(false);
            echo "</div>";
        }
    }?>

    <?= Tabs::widget($tabs); ?>

    <?= RequiredFieldsTipWidget::widget() ?>
    <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>
    <?php
    $config = [
        'model' => $model,
        'urlClose' => $appController->getFormCloseUrl($model)
    ];
    $label = $appController->getFormCloseLabel($model);
    if ($label) {
        $config['closeButtonLabel'] = $label;
    }
    ?>
    <?= CloseSaveButtonWidget::widget($config) ?>
</div>
<?php ActiveForm::end(); ?>
