<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\events
 * @category   CategoryName
 */

use lispa\amos\attachments\components\AttachmentsInput;
use lispa\amos\attachments\components\AttachmentsTableWithPreview;
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use lispa\amos\core\forms\CreatedUpdatedWidget;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\forms\WorkflowTransitionWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\events\AmosEvents;
use lispa\amos\events\models\Event;
use lispa\amos\events\models\EventLengthMeasurementUnit;
use lispa\amos\events\models\EventMembershipType;
use lispa\amos\events\models\search\EventTypeSearch;
use lispa\amos\events\utility\EventsUtility;
use kartik\datecontrol\DateControl;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\redactor\widgets\Redactor;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var lispa\amos\events\models\Event $model
 * @var yii\widgets\ActiveForm $form
 * @var string $fid
 */

\lispa\amos\layout\assets\SpinnerWaitAsset::register($this);

$this->registerJs("
    $('#event-begin_date" . ((isset($fid)) ? $fid : 0) . "').change(function(){
        if($('#event-begin_date" . ((isset($fid)) ? $fid : 0) . "').val() == ''){
            $('#event-begin_date" . ((isset($fid)) ? $fid : 0) . "-disp-kvdate .input-group-addon.kv-date-remove').remove();
        } else {
            if($('#event-begin_date" . ((isset($fid)) ? $fid : 0) . "-disp-kvdate .input-group-addon.kv-date-remove').length == 0){
                $('#event-begin_date" . ((isset($fid)) ? $fid : 0) . "-disp-kvdate').append('<span class=\"input-group-addon kv-date-remove\" title=\"Pulisci campo\"><i class=\"glyphicon glyphicon-remove\"></i></span>');
                initDPRemove('event-begin_date" . ((isset($fid)) ? $fid : 0) . "-disp');
            }
        }
    });
", yii\web\View::POS_READY);

$eventManagementFieldId = Html::getInputId($model, 'event_management');
$registrationLimitDateFieldId = Html::getInputId($model, 'registration_limit_date') . '-disp';
$eventMembershipTypeIdFieldId = Html::getInputId($model, 'event_membership_type_id');
$seatsAvailableFieldId = Html::getInputId($model, 'seats_available');
$paidEventFieldId = Html::getInputId($model, 'paid_event');

$beginDateHourId = lcfirst(Inflector::id2camel(\yii\helpers\StringHelper::basename($model->className()), '_')) . '-begin_date_hour' . ((isset($fid)) ? $fid : 0);

$js = "
    function calcEndDateHour() {
        if (($('#" . $beginDateHourId . "').val() != '') && ($('#event-length').val() != '') && ($('#EventLengthMeasurementUnit').val() != '')) {
            var dataArray = {
                beginDateHour: $('#" . $beginDateHourId . "').val(),
                lengthValue: $('#event-length').val(),
                lengthMUId: $('#EventLengthMeasurementUnit').val()
            };
            $.ajax({
                url: '" . Url::to(['event/calculate-end-date-hour']) . "',
                type: 'post',
                data: dataArray,
                dataType: 'json',
                success: function (response) {
                    $('#event-end_date_hour').val(response.datetime);
                    $('#elem-end-date').html(response.date);
                    $('#elem-end-hour').html(response.time);
                }
            });
        } else {
                $('#event-end_date_hour').val('');
                $('#elem-end-date').html('-');
                $('#elem-end-hour').html('-');
        }
    }

    $('#" . $beginDateHourId . "').on('change', function (event) {
        calcEndDateHour();
    });

    $('#event-length').on('change', function (event) {
        calcEndDateHour();
    });

    $('#EventLengthMeasurementUnit').on('change', function (event) {
        calcEndDateHour();
    });
    
    calcEndDateHour();
    
    $('#country_location_id-id').change(function(){
        if ($('#country_location_id-id').val() == 1) {
            $('#province_location_id-id').prop('disabled', '');
        } else {
            $('#select2-province_location_id-id-container').text('" . AmosEvents::t('amosevents', 'Type the name of the province') . "');
            $('#select2-city_location_id-id-container').text('" . AmosEvents::t('amosevents', 'Select/Choose') . '...' . "');
            $('#province_location_id-id').prop('disabled', 'disabled');
            $('#city_location_id-id').prop('disabled', 'disabled');
        }
    });
    
    function disableEventManagementFields() {
        if ($('#" . $eventManagementFieldId . "').val() == 0) {
            $('#" . $registrationLimitDateFieldId . "').val('').prop('disabled', true);
            $('#" . $eventMembershipTypeIdFieldId . "').val('').trigger('change').prop('disabled', true);
            $('#" . $seatsAvailableFieldId . "').val('').prop('disabled', true);
            $('#" . $paidEventFieldId . "').val('').prop('disabled', true);
            
        }
        if ($('#" . $eventManagementFieldId . "').val() == 1) {
            $('#" . $registrationLimitDateFieldId . "').prop('disabled', false);
            $('#" . $eventMembershipTypeIdFieldId . "').prop('disabled', false);
            $('#" . $seatsAvailableFieldId . "').prop('disabled', false);
            $('#" . $paidEventFieldId . "').prop('disabled', false);
        }
    }

    $('#" . $eventManagementFieldId . "').on('change', function (event) {
        disableEventManagementFields();
    });
    
    disableEventManagementFields();
";
$this->registerJs($js, View::POS_READY);
$moduleEvents = \Yii::$app->getModule(AmosEvents::getModuleName());



$formid = '#event_' . ((isset($fid)) ? $fid : 0);
$js = <<<JS
$('body').on('beforeValidate', '$formid', function (e) {
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
  }).on('afterValidate', '$formid', function (e) {
  if ($(this).find('.has-error').length > 0) {
    $(':input[type="submit"]', this).removeAttr('disabled');
    $(':input[type="submit"]', this).each(function (i) {
      if ($(this).prop('tagName').toLowerCase() === 'input') {
        $(this).val($(this).data('enabled-text'));
      } else {
        $(this).html($(this).data('enabled-text'));
      }
    });
  }else {
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

<div class="loading" hidden></div>
<?php $form = ActiveForm::begin([
    'options' => [
        'id' => 'event_' . ((isset($fid)) ? $fid : 0),
        'data-fid' => (isset($fid)) ? $fid : 0,
        'data-field' => ((isset($dataField)) ? $dataField : ''),
        'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
        'class' => ((isset($class)) ? $class : ''),
        'enctype' => 'multipart/form-data' // important
    ]
]);
$errorSummary = $form->errorSummary($model);
echo $errorSummary;
?>
<?= WorkflowTransitionWidget::widget([
    'form' => $form,
    'model' => $model,
    'workflowId' => Event::EVENTS_WORKFLOW,
    'classDivIcon' => 'pull-left',
    'classDivMessage' => 'pull-left message',
    'viewWidgetOnNewRecord' => true,
    'translationCategory' => 'amosevents'
]); ?>
<div class="event-form col-xs-12 nop">
    <?php // $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
    <?php if ($model->getScenario() == Event::SCENARIO_CREATE || $model->getScenario() == Event::SCENARIO_CREATE_HIDE_PUBBLICATION_DATE ): ?>
        <?php $this->beginBlock('general'); ?>
        <div class="row">
            <div class="col-lg-6 col-sm-6">
                <?php
                $append = '';
                if (\Yii::$app->getUser()->can('EVENTTYPE_CREATE')) {
                    $append = ' canInsert';
                }
                ?>

                <?= $form->field($model, 'event_type_id')->widget(Select2::className(), [
                    'data' => ArrayHelper::map(EventTypeSearch::searchGenericContextEventTypes()->asArray()->all(), 'id', 'title'),
                    'language' => substr(Yii::$app->language, 0, 2),
                    'options' => [
                        'multiple' => false,
                        'id' => 'EventType' . $fid,
                        'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
                        'class' => 'dynamicCreation' . $append,
                        'data-model' => 'event_type',
                        'data-field' => 'title',
                        'data-module' => 'events',
                        'data-entity' => 'event-type',
                        'data-toggle' => 'tooltip'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'pluginEvents' => [
                        "select2:open" => "dynamicInsertOpening"
                    ]
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <?php $this->endBlock(); ?>

        <?php $itemsTab[] = [
            'label' => AmosEvents::tHtml('amosevents', 'General'),
            'content' => $this->blocks['general'],
        ];
        ?>
    <?php else: ?>
        <?php $this->beginBlock('general'); ?>
        <?= $form->field($model, 'event_type_id')->hiddenInput()->label(false) ?>
        <div>
            <h4><strong><?= $model->getAttributeLabel('eventType') ?>:</strong> <?= $model->eventType->title ?></h4>
        </div>
        <div class="row">
            <div class="col-lg-8 col-sm-8">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'summary')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4 col-sm-4">
                <div class="col-xs-12 pull-right">
                    <?= $form->field($model, 'eventLogo')->widget(AttachmentsInput::classname(), [
                        'options' => [ // Options of the Kartik's FileInput widget
                            'multiple' => false, // If you want to allow multiple upload, default to false
                        ],
                        'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                            'maxFileCount' => 1 // Client max files
                        ]
                    ])->label($model->getAttributeLabel('eventLogo')) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <?= $form->field($model, 'description')->widget(Redactor::className(), [
                    'clientOptions' => [
                        'buttonsHide' => [
                            'image',
                            'file'
                        ],
                        'lang' => substr(Yii::$app->language, 0, 2),
                        'placeholder' => AmosEvents::t('amosevents', 'Insert the event description'),
                    ],
                ]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'begin_date_hour')->widget(DateControl::className(), [
                    'type' => DateControl::FORMAT_DATETIME,
                    'options' => [
                        'id' => $beginDateHourId,
                        'layout' => '{input} {picker} ' . (($model->begin_date_hour == '') ? '' : '{remove}')]
                ]); ?>
            </div>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'length')->textInput([
                    'maxlength' => true,
                    'type' => 'number'
                ]) ?>
            </div>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'length_mu_id')->widget(Select2::className(), [
                    'data' => EventsUtility::translateArrayValues(ArrayHelper::map(EventLengthMeasurementUnit::find()->asArray()->all(), 'id', 'title')),
                    'language' => substr(Yii::$app->language, 0, 2),
                    'options' => ['multiple' => false,
                        'id' => 'EventLengthMeasurementUnit' . $fid,
                        'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
                        'class' => 'dynamicCreation',
                        'data-model' => 'event_length_measurement_unit',
                        'data-field' => 'title',
                        'data-module' => 'events',
                        'data-entity' => 'event-length-measurement-unit',
                        'data-toggle' => 'tooltip'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                    'pluginEvents' => [
                        "select2:open" => "dynamicInsertOpening"
                    ]
                ])->label(AmosEvents::tHtml('amosevents', 'Length Measurement Unit'))
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-sm-4">
                <label><?= AmosEvents::tHtml('amosevents', 'End Date') ?></label>
                <div id="elem-end-date"></div>
            </div>
            <div class="col-lg-4 col-sm-4">
                <label><?= AmosEvents::tHtml('amosevents', 'End Hour') ?></label>
                <div id="elem-end-hour"></div>
            </div>
            <?= $form->field($model, 'end_date_hour')->hiddenInput()->label(false) ?>
        </div>
        <div class="row">
            <?php
            $moduleNews = \Yii::$app->getModule(AmosEvents::getModuleName());
            if($moduleNews->hidePubblicationDate == false){?>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'publication_date_begin')->widget(DateControl::className(), [
                    'type' => DateControl::FORMAT_DATE
                ]) ?>
            </div>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'publication_date_end')->widget(DateControl::className(), [
                    'type' => DateControl::FORMAT_DATE
                ]) ?>
            </div>
            <?php }?>
        </div>
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <?= $form->field($model, 'event_location')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($model, 'event_address')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-3 col-sm-3">
                <?= $form->field($model, 'event_address_house_number')->textInput(['maxlength' => true])->label(AmosEvents::tHtml('amosevents', 'House Number')) ?>
            </div>
            <div class="col-lg-3 col-sm-3">
                <?= $form->field($model, 'event_address_cap')->textInput(['maxlength' => true])->label(AmosEvents::tHtml('amosevents', 'CAP')) ?>
            </div>
        </div>
        <div class="row">
            <?php
            echo \lispa\amos\comuni\widgets\helpers\AmosComuniWidget::widget([
                'form' => $form,
                'model' => $model,
                'nazioneConfig' => [
                    'attribute' => 'country_location_id',
                    'class' => 'col-lg-4 col-sm-4'
                ],
                'provinciaConfig' => [
                    'attribute' => 'province_location_id',
                    'class' => 'col-lg-4 col-sm-4'
                ],
                'comuneConfig' => [
                    'attribute' => 'city_location_id',
                    'class' => 'col-lg-4 col-sm-4'
                ],
            ]);
            ?>
        </div>

        <div class="row">
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'event_commentable')->dropDownList(Yii::$app->controller->getBooleanFieldsValues(),
                    ['prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'), 'disabled' => false]) ?>
            </div>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'publish_in_the_calendar')->dropDownList(Yii::$app->controller->getBooleanFieldsValues(),
                    ['prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'), 'disabled' => false]) ?>
            </div>
            <?php
            $disable = false;
            if(!$moduleEvents->enableInvitationManagement) {
                $disable = true;
                $model->event_management = Event::BOOLEAN_FIELDS_VALUE_NO;
            }?>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'event_management')->dropDownList(Yii::$app->controller->getBooleanFieldsValues(),
                    ['prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'), 'disabled' => $disable]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'registration_limit_date')->widget(DateControl::className(), [
                    'type' => DateControl::FORMAT_DATE
                ]) ?>
            </div>
            <div class="col-lg-4 col-sm-4">
                <div class="select">
                    <?= $form->field($model, 'event_membership_type_id')->widget(Select2::classname(), [
                        'options' => ['placeholder' => AmosEvents::t('amosevents', 'Type membership type'), 'id' => 'event_membership_type_id-id', 'disabled' => true],
                        'data' => EventsUtility::translateArrayValues(ArrayHelper::map(EventMembershipType::find()->asArray()->all(), 'id', 'title'))
                    ])->label($model->getAttributeLabel('eventMembershipType')); ?>
                </div>
            </div>
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'seats_available')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-sm-4">
                <?= $form->field($model, 'paid_event')->dropDownList(Yii::$app->controller->getBooleanFieldsValues(),
                    ['prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'), 'disabled' => false]) ?>
            </div>
        </div>

        <div class="clearfix"></div>
        <?php $this->endBlock(); ?>

        <?php $itemsTab[] = [
            'label' => AmosEvents::tHtml('amosevents', 'General'),
            'content' => $this->blocks['general'],
        ];
        ?>

        <?php $this->beginBlock('attachments'); ?>
        <?= $form->field($model, 'eventAttachments')->widget(\lispa\amos\attachments\components\AttachmentsInput::classname(), [
        'options' => [ // Options of the Kartik's FileInput widget
            'multiple' => true, // If you want to allow multiple upload, default to false
        ],
        'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
            'maxFileCount' => 100, // Client max files
			'showPreview' => false,
        ]])->label(AmosEvents::tHtml('amosevents', 'Attachments')) ?>
        <?= AttachmentsTableWithPreview::widget(['model' => $model, 'attribute' => 'eventAttachments']) ?>

        <div class="clearfix"></div>
        <?php $this->endBlock(); ?>

        <?php
        $itemsTab[] = [
            'label' => AmosEvents::tHtml('amosevents', 'Attachments'),
            'content' => $this->blocks['attachments'],
            'options' => ['id' => 'tab-attachments'],
        ];
        ?>
    <?php endif; ?>


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
    );
    ?>
    <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>
    <div class="col-xs-12 note_asterisk nop">
        <p><?= AmosEvents::tHtml('amosevents', 'The fields marked with') ?> <span class="red"> * </span> <?= AmosEvents::tHtml('amosevents', 'are required') ?>.</p>
    </div>

    <?= CloseSaveButtonWidget::widget([
        'model' => $model,
        'urlClose' => Yii::$app->session->get('previousUrl'),
        'buttonNewSaveLabel' => \Yii::t('amoscore', 'crea') ,
        'buttonSaveLabel' => \Yii::t('amoscore', '#save_and_close')
    ]); ?>

    <?php ActiveForm::end(); ?>
</div>
