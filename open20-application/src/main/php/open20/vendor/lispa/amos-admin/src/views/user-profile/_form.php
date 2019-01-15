<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\assets\AmosAsset;
use lispa\amos\admin\assets\ModuleUserProfileAsset;
use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use lispa\amos\core\forms\CreatedUpdatedWidget;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\forms\WorkflowTransitionWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\privileges\widgets\UserPrivilegesWidget;
use kartik\alert\Alert;
use yii\helpers\Url;
use yii\web\View;

AmosAsset::register($this);
ModuleUserProfileAsset::register($this);

/**
 * @var yii\web\View $this
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/* @var \lispa\amos\cwh\AmosCwh $moduleCwh */
$moduleCwh = \Yii::$app->getModule('cwh');

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

// Tabs ids
$idTabCard = 'tab-card';
$idTabInsights = 'tab-insights';
$idTabNetwork = 'tab-network';
$idTabSettings = 'tab-settings';
$idTabAdministration = 'tab-administration';

$defaultFacilitatorId = Html::getInputId($model, 'default_facilitatore');

/** @var \lispa\amos\core\user\User $loggedUser */
$loggedUser = Yii::$app->user->identity;
/** @var UserProfile $loggedUserProfile */
$loggedUserProfile = $loggedUser->getProfile();
//$ajaxUrl = Url::to(['/admin/user-profile/is-default-facilitator', 'id' => $loggedUserProfile->id]);
$ajaxUrl = Url::to(['/admin/user-profile/def-facilitator-present', 'id' => $model->id]);
$js = "
var isPreviousChecked = $('#$defaultFacilitatorId').is(':checked');
var isProfileModified = 0;

$('#$defaultFacilitatorId').on('change', function(event) {
    var isChecked = $(this).is(':checked');
    var ajaxData = {
        isChecked: isChecked
    };
    var errorMsg = '';
    $.ajax({
        url: '$ajaxUrl',
        type: 'post',
        dataType: 'json',
        success: function (response) {
            if (response.defaultFaciliatorPresent) {
                if (" . $loggedUserProfile->id . " == " . $model->id . ") {
                    if (isPreviousChecked && !isChecked) {
                        var ok = confirm('" . AmosAdmin::t('amosadmin', 'Attention! There will no longer be any default facilitator. Confirm?') . "');
                        $('#$defaultFacilitatorId').prop('checked', !ok);
                        isPreviousChecked = !ok;
                    } else if (!isPreviousChecked && isChecked) {
                        isPreviousChecked = true;
                    }
                } else {
                    if (!isPreviousChecked && isChecked) {
                        $('#$defaultFacilitatorId').prop('checked', false);
                        alert('" . AmosAdmin::t('amosadmin', 'Operation impossible because') . " ' + response.facilitatorNameSurname + ' " . AmosAdmin::t('amosadmin', 'is already referred to as default facilitator.') . "');
                    }
                }
            } else {
                if (isPreviousChecked && !isChecked) {
                    var ok = confirm('" . AmosAdmin::t('amosadmin', 'Attention! There will no longer be any default facilitator. Confirm?') . "');
                    $('#$defaultFacilitatorId').prop('checked', !ok);
                    isPreviousChecked = !ok;
                } else if (!isPreviousChecked && isChecked) {
                    isPreviousChecked = true;
                }
            }
        },
        error: function() {
            alert('" . AmosAdmin::t('amosadmin', 'AJAX error while checking default facilitator presence') . "');
        }
    });
});

";

$jsIsProfileModified = "
    $('#tab-card, #tab-insights, #amos-cwh-tag').on('change', 'input, select, textarea', function(e) {
      isProfileModified = 1;
      $('#isProfileModified').val('1');
    });
    
    $('.saveBtn').on('click', function(e) {
        e.preventDefault();
        if(isProfileModified == 1){
           $(modalId).modal('show'); 
        }else{
            $('form').submit();  
        }
      return false;
    });
";

if ($model->id) {
    $this->registerJs($js, View::POS_READY);
    if ($model->status == UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED && $model->status != $model->getWorkflowSource()->getWorkflow(UserProfile::USERPROFILE_WORKFLOW)->getInitialStatusId()) {
        $this->registerJs($jsIsProfileModified, View::POS_READY);
    }
}
$enableUserContacts = AmosAdmin::getInstance()->enableUserContacts;


$formid = '#user-profile_' . ((isset($fid)) ? $fid : 0);
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
        'id' => 'user-profile_' . ((isset($fid)) ? $fid : 0),
        'data-fid' => (isset($fid)) ? $fid : 0,
        'data-field' => ((isset($dataField)) ? $dataField : ''),
        'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
        'class' => 'default-form col-xs-12 nop',
        'enctype' => 'multipart/form-data' // important
    ]
]);
?>

<?= WorkflowTransitionWidget::widget([
    'form' => $form,
    'model' => $model,
    'workflowId' => UserProfile::USERPROFILE_WORKFLOW,
    'classDivIcon' => 'pull-left',
    'classDivMessage' => 'pull-left message',
    'viewWidgetOnNewRecord' => true,
    'translationCategory' => 'amosadmin'
]); ?>

<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>

<div class="details_card">
    <div class="profile row nom">

        <div class="col-sm-4 col-md-3 col-xs-12 left-column">
            <div class="img-profile">
                <?php if ($adminModule->confManager->isVisibleBox('box_foto', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_foto', [
                        'form' => $form,
                        'model' => $model,
                        'user' => $user
                    ]); ?>
                <?php endif; ?>
                <div class="under-img">
                    <?php if ($adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                        <h2><?= ($model->nome ? $model->nome : '') ?>
                            <?php if ($adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                                <br class="hidden-xs"/>
                                <?= ($model->cognome ? $model->cognome : '') ?>
                            <?php endif; ?>
                        </h2>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-sm-8 col-md-9 col-xs-12 right-column">

            <?php $this->beginBlock($idTabCard); ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_informazioni_base', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_informazioni_base', ['form' => $form, 'model' => $model, 'user' => $user, 'idTabInsights' => $idTabInsights]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_dati_contatto', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_dati_contatto', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_facilitatori', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_facilitatori', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_privacy', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?php
                // ADMIN cannot check the privacy checkbox of other users
                if (!(\Yii::$app->user->can('ADMIN') && $user->id !== \Yii::$app->user->id)) { ?>
                    <?= $this->render('boxes/box_privacy', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php } else {
                    echo "<div class='col-lg-12 m-t-10'><strong>" . $model->getAttributeLabel('privacy') . " :</strong>  " . \Yii::$app->formatter->asBoolean($model->privacy) . "</div>";
                } ?>
            <?php endif; ?>

            <?php $this->endBlock(); ?>
            <?php
            $itemsTab[] = [
                'label' => AmosAdmin::t('amosadmin', 'Card'),
                'content' => $this->blocks[$idTabCard],
                'options' => ['id' => $idTabCard]
            ];
            ?>

            <?php $this->beginBlock($idTabInsights); ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_presentazione_personale', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_presentazione_personale', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_role_and_area', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?php if ($adminModule->confManager->isVisibleBox('box_presentazione_personale', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <hr>
                <?php endif; ?>
                <?= $this->render('boxes/box_role_and_area', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_prevalent_partnership', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_prevalent_partnership', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_ulteriori_informazioni', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_ulteriori_informazioni', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_dati_residenza', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_dati_residenza', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_dati_nascita', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_dati_nascita', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_dati_fiscali_amministrativi', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_dati_fiscali_amministrativi', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?php $this->endBlock(); ?>
            <?php
            $itemsTab[] = [
                'label' => AmosAdmin::t('amosadmin', 'Insights'),
                'content' => $this->blocks[$idTabInsights],
                'options' => ['id' => $idTabInsights]
            ];
            ?>

            <?php if ($model->isNewRecord || ($enableUserContacts && $model->validato_almeno_una_volta) || isset($moduleCwh)): ?>
                <?php $this->beginBlock($idTabNetwork); ?>
                <div>
                    <?php if ($model->isNewRecord): ?>
                        <?= Alert::widget([
                            'type' => Alert::TYPE_WARNING,
                            'body' => AmosAdmin::t('amosadmin', "Prima di poter gestire la rete &egrave; necessario salvare l'utente."),
                            'closeButton' => false
                        ]); ?>
                    <?php else: ?>
                        <?php
                        if ($enableUserContacts && $model->validato_almeno_una_volta) {
                            echo \lispa\amos\admin\widgets\UserContacsWidget::widget([
                                'userId' => $model->user_id,
                                'isUpdate' => true
                            ]);
                        }
                        if (isset($moduleCwh)) {
                            echo \lispa\amos\cwh\widgets\UserNetworkWidget::widget([
                                'userId' => $model->user_id,
                                'isUpdate' => true
                            ]);
                        }
                        ?>
                    <?php endif; ?>
                </div>
                <?php $this->endBlock(); ?>
                <?php
                $itemsTab[] = [
                    'label' => AmosAdmin::t('amosadmin', 'Network'),
                    'content' => $this->blocks[$idTabNetwork],
                    'options' => ['id' => $idTabNetwork]
                ];
                ?>
            <?php endif; ?>

            <?php if (Yii::$app->user->can('ADMIN') || Yii::$app->user->can('UpdateOwnUserProfile', ['model' => $loggedUserProfile])): ?>
                <?php $this->beginBlock($idTabSettings); ?>
                <div>
                    <?php if ($model->isNewRecord): ?>
                        <?= Alert::widget([
                            'type' => Alert::TYPE_WARNING,
                            'body' => AmosAdmin::t('amosadmin', 'Prima di poter gestire le classi di utenza &egrave; necessario salvare l\'utente.'),
                            'closeButton' => false
                        ]); ?>
                    <?php else: ?>

                        <?php if ($adminModule->confManager->isVisibleBox('box_social_account', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                            <?= $this->render('boxes/box_social_account', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                        <?php endif; ?>

                        <?php if ($adminModule->confManager->isVisibleBox('box_email_frequency', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                            <?= $this->render('boxes/box_email_frequency', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                        <?php endif; ?>

                        <?php if ($adminModule->confManager->isVisibleBox('box_dati_accesso', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                            <?= $this->render('boxes/box_dati_accesso', ['form' => $form, 'model' => $model, 'user' => $user, 'spediscicredenzialienable' => isset($spediscicredenzialienable) ? $spediscicredenzialienable : true]); ?>
                        <?php endif; ?>

                        <?php if ($adminModule->confManager->isVisibleBox('box_account_data', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                            <?= $this->render('boxes/box_account_data', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                        <?php endif; ?>

                        <?php if ($adminModule->confManager->isVisibleBox('box_questio', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                            <?= $this->render('boxes/box_questio', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                        <?php endif; ?>

                        <?php if (Yii::$app->user->can('ADMIN') && $model->isFacilitator() && $adminModule->confManager->isVisibleField('default_facilitatore', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                            <div class="col-xs-12">
                                <?= $form->field($model, 'default_facilitatore')->checkbox()->label(AmosAdmin::t('amosadmin', 'Is default facilitator') . '?') ?>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
                <?php $this->endBlock(); ?>
                <?php
                $itemsTab[] = [
                    'label' => AmosAdmin::t('amosadmin', 'Settings'),
                    'content' => $this->blocks[$idTabSettings],
                    'options' => ['id' => $idTabSettings],
                ];
                ?>
            <?php endif; ?>

            <?php if (Yii::$app->user->can('PRIVILEGES_MANAGER')): ?>
                <?php $privilegesModule = Yii::$app->getModule('privileges'); ?>
                <?php if (!empty($privilegesModule)): ?>
                    <?php $this->beginBlock($idTabAdministration); ?>
                    <?php if ($model->isNewRecord): ?>
                        <?= Alert::widget([
                            'type' => Alert::TYPE_WARNING,
                            'body' => AmosAdmin::t('amosadmin', 'Prima di poter gestire le classi di utenza &egrave; necessario salvare l\'utente.'),
                            'closeButton' => false
                        ]) ?>
                    <?php else: ?>
                        <?= UserPrivilegesWidget::widget(['userId' => $model->user_id]) ?>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                    <?php $this->endBlock(); ?>
                    <?php
                    $itemsTab[] = [
                        'label' => AmosAdmin::t('amosadmin', 'Administration'),
                        'content' => $this->blocks[$idTabAdministration],
                        'options' => ['id' => $idTabAdministration],
                    ];
                    ?>
                <?php endif; ?>
            <?php endif; ?>

            <?php
            if (isset($tabActive) && isset($itemsTab[$tabActive])) {
                $itemsTab[$tabActive]['active'] = true;
            }
            ?>

            <?php //echo Html::errorSummary($model);  ?>

            <?= Tabs::widget([
                'encodeLabels' => false,
                'items' => $itemsTab,
                /* 'clientEvents' => [
                  'shown.bs.tab' => new JsExpression('reloadGoogleMaps')
                  ] */
            ]); ?>

            <div class="col-xs-12 note_asterisk nop">
                <p><?= AmosAdmin::t('amosadmin', 'I campi contrassegnati con') ?> <span
                            class="red">*</span> <?= AmosAdmin::t('amosadmin', 'sono obbligatori.') ?></p>
            </div>
            <?= $form->field($model, 'tipo_utente')->hiddenInput()->label('AmosHidden') ?>

            <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>

            <?= $form->field($model, 'isProfileModified')->hiddenInput(['id' => 'isProfileModified', 'value' => '0'])->label(false) ?>

            <?php
            if (!$model->isNewRecord) {
                \yii\bootstrap\Modal::begin(['id' => 'modalId']);
                echo Html::tag('div', AmosAdmin::t('amosadmin', 'Per aggiornare il profilo è necessario rimetterlo in stato MODIFICA IN CORSO e, se vorrai che sia nuovamente validato, dovrai richiederna la validazione. Confermi ?'));
                echo Html::tag('div',
                    Html::submitButton(AmosAdmin::t('amosadmin', 'Ok'),
                        ['class' => 'btn btn-navigation-primary', 'id' => $model->getWorkflowStatus()->getId()])
                    . Html::a(AmosAdmin::t('amosadmin', 'Annulla'), null, ['data-dismiss' => 'modal', 'class' => 'btn btn-secondary']),
                    ['class' => 'pull-right m-15-0']
                );
                \yii\bootstrap\Modal::end();
            }

            $closeSaveOptions = [
                'model' => $model,
                'buttonClassSave' => 'btn btn-navigation-primary saveBtn',
            ];
            ?>
            <?= CloseSaveButtonWidget::widget($closeSaveOptions); ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
