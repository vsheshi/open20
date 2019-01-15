<?php

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\utilities\CommunityUtil;
use lispa\amos\community\widgets\CommunityMembersWidget;
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use lispa\amos\core\forms\CreatedUpdatedWidget;
use lispa\amos\core\forms\PublishedContentsWidget;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var lispa\amos\community\models\Community $model
 * @var yii\widgets\ActiveForm $form
 */

$modelsEnabled = Yii::$app->getModule('cwh')->modelsEnabled;

$idTabContents = 'tab-contents';
$idTabSheet = 'tab-registry';
$idTabParticipants = 'tab-participants';
$idTabSubcommunities = 'tab-subcommunities';
//check if a specific tab must be the active one
$tabContentsActive = isset($tabActive) ? ((strcmp($tabActive, $idTabContents) == 0) ? true : false) : false;
$tabSheetActive = isset($tabActive) ? ((strcmp($tabActive, $idTabSheet) == 0) ? true : false) : true;
$tabParticipantsActive = isset($tabActive) ? ((strcmp($tabActive, $idTabParticipants) == 0) ? true : false) : false;
$tabSubcommunitiesActive = isset($tabActive) ? ((strcmp($tabActive, $idTabSubcommunities) == 0) ? true : false) : false;

$communities = CommunityUtil::getParentList();
$showSubcommunityField = (!empty($communities) && ($model->isNewRecord || $model->parent_id != null));
/** @var AmosCommunity $moduleCommunity */
$moduleCommunity = Yii::$app->getModule('community');
$fixedCommunityType = !is_null($moduleCommunity->communityType);
$viewTabContents = $moduleCommunity->viewTabContents;
$bypassWorkflow = $moduleCommunity->bypassWorkflow;
$showSubcommunities = $moduleCommunity->showSubcommunities;

$js = <<<JS
var modalId = '#visibleOnEditPopup-$model->id';
var valDescription = $('.description-redactor').val();

$('#tab-registry').on('change', 'input, select, textarea', function(e) {
  $('#community-backtoedit').val(1);
});

$(modalId).on('click', '#visibleYes', function(e) {
  $('#community-visible_on_edit').val(1);
});

$(modalId).on('click', '#visibleNot', function(e) {
   e.preventDefault();
   $('#community-visible_on_edit').val(0);
   $(modalId).modal('hide'); 
   $('#warningPopup').modal('show'); 
});

$('#warningPopup').on('click', '#ok-warning', function(e) {
    $('form').submit();  
});

$('#saveBtn').on('click', function(e) {
    e.preventDefault();
        if($('.description-redactor').val() !== valDescription) {
          $('#community-backtoedit').val(1);
    }
    if($(this).data('target') == modalId && $('#community-backtoedit').val() == 1){
       $(modalId).modal('show'); 
    }else{
       $('form').submit();  
    }
  return false;
});

JS;

$this->registerJs($js);

$formid = '#community_form_' . $model->id;
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
  }
});
JS;

$this->registerJs($js, View::POS_READY);

?>

<?php
$form = ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'form default-form col-xs-12 nop',
        'id' => 'community_form_' . $model->id,
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'error-summary alert alert-error'
    ],
]);
?>

<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>

<?= \lispa\amos\core\forms\WorkflowTransitionWidget::widget([
    'form' => $form,
    'model' => $model,
    'workflowId' => \lispa\amos\community\models\Community::COMMUNITY_WORKFLOW,
    'classDivIcon' => 'pull-left',
    'classDivMessage' => 'pull-left message',
    'viewWidgetOnNewRecord' => true
]); ?>

<div class="details_card">
    <div class="profile row nom">
        <div class="col-sm-4 col-md-3 col-xs-12 left-column">
            <div class="img-profile">
                <?= $form->field($model, 'communityLogo')->widget(\lispa\amos\attachments\components\AttachmentsInput::classname(), [
                    'options' => [ // Options of the Kartik's FileInput widget
                        'multiple' => false, // If you want to allow multiple upload, default to false
                    ],
                    'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                        'maxFileCount' => 1,// Client max files
                        'showRemove' => false,// Client max files,
                        'indicatorNew' => false,
                        'allowedPreviewTypes' => ['image'],
                        'previewFileIconSettings' => false,
                        'overwriteInitial' => false,
                        'layoutTemplates' => false,
                        'minFileSize' => null
                    ]
                ])->label($model->getAttributeLabel('communityLogo')) ?>
                <div class="under-img">
                    <?php if ($model->name): ?>
                        <h2><?= $model->name ?></h2>
                    <?php endif; ?>
                </div>
            </div>


            <div class="container-info-icons">


            </div>
        </div>
        <div class="col-sm-8 col-md-9 col-xs-12 right-column">
            <?php if($viewTabContents): ?>
                <?php
                ////////////////////////////*********************************** BEGIN TAB CONTENTS ***********************************////////////////////////////
                $this->beginBlock($idTabContents);
                ?>
                <div class="body">
                    <div class="intestazione-box">
                        <?php Html::tag('h3', AmosCommunity::tHtml('amoscommunity', 'Contents')) ?>
                    </div>

                    <?php if (!empty($modelsEnabled) && !$model->isNewRecord) {
                        foreach ($modelsEnabled as $modelEnabled) { ?>
                            <?= PublishedContentsWidget::widget([
                                'modelClass' => $modelEnabled,
                                'scope' => ['community' => $model->id],
                                'isUpdate' => true
                            ]) ?>
                        <?php }
                    } ?>
                </div>
                <?php
                $this->endBlock();
                ////////////////////////////************************************ END TAB CONTENTS ************************************////////////////////////////
                ?>
            <?php endif; ?>
            <?php
            ////////////////////////////********************************** BEGIN TAB SHEET **********************************////////////////////////////
            $this->beginBlock($idTabSheet);
            ?>
            <div class="body">

                <h2>
                    <?= AmosIcons::show('account'); ?>
                    <?= AmosCommunity::tHtml('amoscommunity', 'Base information') ?>
                </h2>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <br/>
                <?= $form->field($model, 'description')->widget(yii\redactor\widgets\Redactor::className(), [
                    'options' => [
                        'id' => 'description' . $model->id,
                        'class' => 'description-redactor'
//                            'limiter' => 500
                    ],
                    'clientOptions' => [
                        'buttonsHide' => [
                            'image',
                            'file'
                        ],
                        'lang' => substr(Yii::$app->language, 0, 2),
                        'placeholder' => AmosCommunity::t('amoscommunity', 'Insert the community description'),
                    ],
                ])->label($model->getAttributeLabel('description'), ['for' => 'description' . $model->id]);
                ?>
                <?= $form->field($model, 'backToEdit')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'visible_on_edit')->hiddenInput()->label(false) ?>

                <?php if(!$fixedCommunityType || ($showSubcommunityField && $showSubcommunities)): ?>
                    <h2>
                        <?= AmosIcons::show('info'); ?>
                        <?= AmosCommunity::tHtml('amoscommunity', 'Additional information') ?>
                    </h2>
                    <?php if(!$fixedCommunityType): ?>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'community_type_id')->widget(Select2::className(), [
                                'data' => CommunityUtil::translateArrayValues(ArrayHelper::map(\lispa\amos\community\models\CommunityType::find()->asArray()->all(), 'id', 'name')),
                                'language' => substr(Yii::$app->language, 0, 2),
                                'options' => ['multiple' => false,
                                    'id' => 'communityType' . $model->id,
                                    'placeholder' => AmosCommunity::t('amoscommunity', 'Select') . '...',
                                    'class' => 'dynamicCreation',
                                    'data-model' => 'community-type',
                                    'data-field' => 'id',
                                    'data-module' => 'community',
                                    'data-entity' => 'community-type',
                                    'data-toggle' => 'tooltip',
                                    'disabled' => (!$model->isNewRecord) && ($model->community_type_id != null)
                                ],
                                'pluginOptions' => [
                                    'allowClear' => $model->isNewRecord
                                ],
                                'pluginEvents' => [
                                    "select2:open" => "dynamicInsertOpening"
                                ]
                            ])->label(AmosCommunity::t('amoscommunity', 'Type'), ['for' => 'communityType' . $model->id]) ?>
                        </div>
                    <?php endif;?>
                    <?php if ($showSubcommunityField && $showSubcommunities): ?>
                        <?php if(!empty($model->parent_id)) {
                            $communityParent = Community::findOne($model->parent_id);?>
                            <div class="col-sm-12">
                                <?= $form->field($model, 'parent_id')->hiddenInput()->label(false);?>
                                <p><strong><?=AmosCommunity::t('amoscommunity', "Created under the scope of community/organization:")?> : </strong><?= $communityParent->name?></p>
                            </div>
                        <?php } else { ?>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'parent_id')->widget(\kartik\select2\Select2::className(), [
                                    'data' => $communities,
                                    'options' => [
                                        'multiple' => false,
                                        'id' => 'parent_id',
                                        'placeholder' => AmosCommunity::t('amoscommunity', 'Select') . '...',
                                        'class' => 'dynamicCreation',
                                        'data-field' => 'id',
                                        'disabled' => !$model->isNewRecord
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => $model->isNewRecord && Yii::$app->user->can('COMMUNITY_CREATOR')
                                    ],
                                ])->label(AmosCommunity::t('amoscommunity', "Created under the scope of community/organization:")) ?>
                            </div>
                        <?php }?>
                    <?php endif; ?>
                <?php endif;?>
            </div>
            <?php
            $this->endBlock();
            ////////////////////////////*********************************** END TAB SHEET***********************************////////////////////////////
            ?>
            <?php
            ////////////////////////////********************************* INIZIO TAB PARTICIPANTS *********************************////////////////////////////
            $this->beginBlock($idTabParticipants);
            ?>
            <div class="body">
                <div class="intestazione-box">
                    <?php Html::tag('h3', AmosCommunity::tHtml('amoscommunity', 'Participants')) ?>
                </div>
                
                <?php
                if (!$model->isNewRecord) {
//                    pr($model->attributes);
                    echo CommunityMembersWidget::widget([
                        'model' => $model,
                        'targetUrlParams' => [
                            'viewM2MWidgetGenericSearch' => true
                        ],
                    ]);
                }
                ?>
            </div>
            <?php
            $this->endBlock();
            ////////////////////////////********************************** END TAB PARTICIPANTS **********************************////////////////////////////
            ?>
            <?php
            ////////////////////////////*********************************** BEGIN TAB SUBCOMMUNITIES ***********************************////////////////////////////
            $this->beginBlock($idTabSubcommunities);
            ?>
            <div class="body">
                <div class="intestazione-box">
                    <?php Html::tag('h3', AmosCommunity::tHtml('amoscommunity', 'Subcommunities')) ?>
                </div>
                <?php
                if (!$model->isNewRecord) {
                    try {
                        echo \lispa\amos\community\widgets\SubcommunitiesWidget::widget([
                            'model' => $model,
                            'isUpdate' => true,
                            'createNewBtnOtherOptions' => [
                                // 'data-confirm' => AmosCommunity::t('amoscommunity', 'Per non perdere i dati inseriti, salva l\'area di lavoro che stai modificando. Vuoi continuare con la creazione di una nuova stanza?'),
                                'modalMessage' => AmosCommunity::t('amoscommunity', "Per non perdere i dati inseriti, salva l'area di lavoro che stai modificando. Vuoi continuare con la creazione di una nuova stanza?"),
                                'data-toggle' => 'modal',
                                'data-target' => '#new-room-modal-' . $model->id,
                            ],
                        ]);
                    } catch (Exception $e) {
                        pr($e->getMessage(),'ERROR');
                    }
                }
                ?>
            </div>
            <?php
            $this->endBlock();
            ////////////////////////////************************************ END TAB SUBCOMMUNITIES ************************************////////////////////////////
            ?>
            
            <?php
            if ($viewTabContents && !$model->isNewRecord) {
                $itemsTab[] = [
                    'label' => AmosCommunity::tHtml('amoscommunity', 'Contents'),
                    'content' => $this->blocks[$idTabContents],
                    'options' => ['id' => $idTabContents],
                    'active' => $tabContentsActive
                ];
            }
            $itemsTab[] = [
                'label' => AmosCommunity::tHtml('amoscommunity', 'Registry'),
                'content' => $this->blocks[$idTabSheet],
                'options' => ['id' => $idTabSheet],
                'active' => $tabSheetActive
            ];
            ?>
            <?php
            if (!$model->isNewRecord) {
                $itemsTab[] = [
                    'label' => AmosCommunity::tHtml('amoscommunity', 'Participants'),
                    'content' => $this->blocks[$idTabParticipants],
                    'options' => ['id' => $idTabParticipants],
                    'active' => $tabParticipantsActive
                ];
                $itemsTab[] = [
                    'label' => AmosCommunity::tHtml('amoscommunity', 'Subcommunities'),
                    'content' => $this->blocks[$idTabSubcommunities],
                    'options' => ['id' => $idTabSubcommunities],
                    'active' => $tabSubcommunitiesActive
                ];
            }
            ?>
            <?= Tabs::widget(
                [
                    'encodeLabels' => false,
                    'items' => $itemsTab
                ]
            );
            ?>
            <div class="col-xs-12 note_asterisk nop">
                <p><?= AmosCommunity::tHtml('amoscommunity', 'The fields marked with ') ?><span class="red">*</span><?= AmosCommunity::tHtml('amoscommunity', ' are required') ?></p>
            </div>
            <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>

            <?php

            Modal::begin(['id' => 'visibleOnEditPopup-'.$model->id]);
            echo Html::tag('div', AmosCommunity::t('amoscommunity',
                "Community status will be set to editing in progress. Keep the community visible while editing it?"));
            echo Html::tag('div',
                Html::submitButton(AmosCommunity::t('amoscommunity', 'Yes'),
                    ['class' => 'btn btn-navigation-primary', 'id' => 'visibleYes'])
                . Html::submitButton(AmosCommunity::t('amoscommunity', 'No'), ['class' => 'btn btn-secondary' , 'id' => 'visibleNot'])
                . Html::a(AmosCommunity::t('amoscommunity', 'Annulla'), null, ['data-dismiss' => 'modal' , 'class' => 'btn btn-secondary']),
                ['class' => 'pull-right m-15-0']
            );
            Modal::end();

            Modal::begin(['id' => 'warningPopup']);
            echo Html::tag('div', AmosCommunity::t('amoscommunity',
                "Attenzione, quando si modifica la descrizione di una community è necessario procedere alla richiesta di validazione.
                La community non risulterà visibile agli utenti fino a quando non sarà nuovamente validata."));
            echo Html::tag('div',
                Html::a(AmosCommunity::t('amoscommunity', 'Ok'), null, ['data-dismiss' => 'modal' , 'class' => 'btn btn-secondary', 'id' => 'ok-warning'])
                . Html::a(AmosCommunity::t('amoscommunity', 'Annulla'), null, ['data-dismiss' => 'modal' , 'class' => 'btn btn-secondary']),
                ['class' => 'pull-right m-15-0']
            );
            Modal::end();

            echo CloseSaveButtonWidget::widget(['model' => $model,
                'buttonId' => 'saveBtn',
                'dataTarget' => (!$bypassWorkflow && ($model->status == Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED) && !$model->isNewRecord ? '#visibleOnEditPopup-'.$model->id : ''),
                'dataToggle' => 'modal',
            ]);

            ?>
        </div>
    </div>
</div>

<?php
ActiveForm::end();
?>
