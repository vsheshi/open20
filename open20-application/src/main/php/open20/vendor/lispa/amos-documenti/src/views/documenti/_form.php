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
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var lispa\amos\documenti\models\Documenti $model
 * @var yii\widgets\ActiveForm $form
 */

/** @var \lispa\amos\documenti\controllers\DocumentiController $appController */
$appController = Yii::$app->controller;
$isFolder = $appController->documentIsFolder($model);
$enableCategories = AmosDocumenti::instance()->enableCategories;
$moduleGroups = Yii::$app->getModule('groups');
$moduleCommunity = Yii::$app->getModule('community');
$moduleCwh = Yii::$app->getModule('cwh');

\yii\web\YiiAsset::register($this);
\lispa\amos\layout\assets\SpinnerWaitAsset::register($this);

$enableGroupNotification = AmosDocumenti::instance()->enableGroupNotification;

if($enableGroupNotification) {

    $modelSearchProfile = new \lispa\amos\admin\models\search\UserProfileSearch();
    $dataProviderProfiles = $modelSearchProfile->search(\Yii::$app->request->get());
    $dataProviderProfiles->setSort([
        'defaultOrder' => [
            'nome' => SORT_ASC
        ]
    ]);
    $dataProviderProfiles->pagination = false;
    $idCommunityMembers = implode(',',$dataProviderProfiles->keys);

    $idsNotifichePreferenzeProfili = $model->getNotifichePreferenzeProfili();
    $idsProfili = implode(',',$idsNotifichePreferenzeProfili);
    $idsNotifichePreferenzeGruppi = $model->getNotifichePreferenzeGruppi();
    $idsGruppi = implode(',',$idsNotifichePreferenzeGruppi);

    $isNew = $model->isNewRecord? 'true': 'false';

$js = <<< JS
$(document).ready(function () {
    var selectedProfiles = [$idCommunityMembers];
    var preferenseProfili = [$idsProfili];
    var preferenseGruppi = [$idsGruppi];
    
    initialize();

    function setChecked() {
        $('#grid-members tbody tr').each(function () {
            var valore = $(this).find('input').val();
            var flag = 0;

            var lista = preferenseProfili;
            if ($isNew) {
                lista = selectedProfiles
            }
        
            for (var i = 0; i < lista.length; i++) {
                if (lista[i] == valore) {
                    $(this).find('input').attr('checked', true);
                    $(this).addClass('success');
                    flag = 1;
                }
            }

            if (flag == 0) {
                $(this).removeClass('success');
                $(this).find('input').removeAttr('checked');
            }
            
        });
        
        $('#w4 tbody tr').each(function () {
            var valore = $(this).find('input').val();
            var flag = 0;
            
            for (var i = 0; i < preferenseGruppi.length; i++) {
                if (preferenseGruppi[i] == valore) {
                    $(this).find('input').attr('checked', true);
                    $(this).addClass('success');
                    flag = 1;
                }
            }

            if (flag == 0) {
                $(this).removeClass('success');
                $(this).find('input').removeAttr('checked');
            }
        });
    }

    
    //check SINGOLO partecipanti
    $(document).on('click', '#grid-members .kv-row-checkbox', function () {
        var tr = $(this).closest('tr');
        var user_profile_id = $(tr).attr('data-key');
        if (this.checked) {
            selectedProfiles.push(user_profile_id);
            $('<input>').attr({
                type: 'hidden',
                id: 'profile-' + user_profile_id,
                name: 'selection-profiles[]',
                value: user_profile_id
            }).appendTo('form');
        }
        else {
            //remove selection
            for (var i = selectedProfiles.length - 1; i >= 0; i--) {
                if (selectedProfiles[i] === user_profile_id) {
                    selectedProfiles.splice(i, 1);
                }
            }
            $('#profile-' + user_profile_id).remove();
        }

    });

    $(document).on('pjax:end', function (data, status, xhr, options) {
        setChecked();
    });
    

    //check ALL partecipanti
    $('#grid-members .select-on-check-all').click(function () {
        
        if (!this.checked) {
            for (var i = 0; i < selectedProfiles.length; i++) {
                $('#profile-' + selectedProfiles[i]).remove();
                $('#grid-members tbody tr[data-key=' + selectedProfiles[i] + ']').removeClass('success');
            }
            selectedProfiles = [];
        }
        else {
            selectedProfiles = [$idCommunityMembers];
            for (var j = 0; j < selectedProfiles.length; j++) {
                if ($('#profile-' + selectedProfiles[j]).length == 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'profile-' + selectedProfiles[j],
                        name: 'selection-profiles[]',
                        value: selectedProfiles[j]
                    }).appendTo('form');
                    $('#grid-members tbody tr[data-key=' + selectedProfiles[j] + ']').addClass('success');
                }
            }
        }
    });

    function initialize() {
        var lista = preferenseProfili;
        if ($isNew) {
            lista = selectedProfiles
        }
        for (var i = 0; i < lista.length; i++) {
            $('<input>').attr({
                type: 'hidden',
                id: 'profile-' + lista[i],
                name: 'selection-profiles[]',
                value: lista[i]
            }).appendTo('form');
        }

        setChecked();
    }
    
    $(document).on('click', '.button-delete-attach', function(event){
       event.preventDefault();
           if(confirm('Sei sicuro di volere eliminare questo elemento?')){
           $.ajax({
              type: "POST",
              url: $(this).attr('href')
            }).done(function(){
                $.pjax.reload({container:"#pjax-container-attach", timeout:20000});
            });
       }
       return false;
    });
});

JS;

    $this->registerJs($js);

}



$js = <<<JS
$('body').on('beforeValidate', '#doc-form', function (e) {
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
  }).on('afterValidate', '#doc-form', function (e) {
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
  else {
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
<?php
$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'], // important
	'id' => 'doc-form'
]);
$customView = Yii::$app->getViewPath() . '/imageField.php';
$errorSummary = $form->errorSummary($model);
?>

<?php //if($model->getErrors()) {?>
<!--    <div class="col-xs-12 alert alert-danger">-->
        <?= $errorSummary; ?>
<!--    </div>-->
<?php // } ?>

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
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-container-attach', 'timeout' => 20000, 'enablePushState' => false,
            'enableReplaceState' => false,]); ?>
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
        <?php \yii\widgets\Pjax::end();?>

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

    <?php if($enableGroupNotification && !$model->is_folder) { ?>
        <?php $this->beginBlock('email-notification'); ?>
        <div class="col-lg-12 col-sm-12">
            <h4> <?= AmosDocumenti::t('amosdocumenti', '#email_notification_tex1') ?></h4>
        </div>
        <?php if (!empty($moduleGroups) && !empty($moduleCommunity) && !empty($moduleCwh)) {
            echo " ";
            $entityId = null;
            $this->params['idUserprofileCommunity'] = [];
//            $moduleCwh->setCwhScopeFromSession();
//            if (!empty($moduleCwh->userEntityRelationTable)) {
//                $entityId = $moduleCwh->userEntityRelationTable['entity_id'];
//                $community = \lispa\amos\community\models\Community::findOne($entityId);
//                if(!empty($community)) {
//                    $usersMms = $community->communityUserMms;
//                    foreach ($usersMms as $memberComm){
//                        $userProfileCommunity = \lispa\amos\admin\models\UserProfile::find()->andWhere(['user_id' => $memberComm->user_id])->one();
//                        if(!empty($userProfileCommunity)){
//                            $this->params['idUserprofileCommunity'][] = $userProfileCommunity->user->id;
//                        }
//                    }
//                }
//            }

            if (isset($moduleCommunity)) {
                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => \lispa\amos\groups\models\Groups::getGroupsByParent()
                ]);
            } else {
                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => \lispa\amos\groups\models\Groups::find()->andWhere(0)
                ]);
            }
            ?>


            <div class="col-xs-12 col-lg-6">
                <div class="col-lg-12">
                    <h3><?= AmosDocumenti::t('amosdocumenti', 'Partecipanti')?></h3>
                </div>
                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-container', 'timeout' => 2000, 'clientOptions' => ['data-pjax-container' => 'grid-members']]); ?>
                <?php echo \lispa\amos\core\views\AmosGridView::widget([
                    'dataProvider' => $dataProviderProfiles,
                    'id' => 'grid-members',
                    'columns' => [
                        'nomeCognome',
                        [
                            'class' => '\kartik\grid\CheckboxColumn',
                            'rowSelectedClass' => \kartik\grid\GridView::TYPE_SUCCESS,
                            'name' => 'element-profiles',
//                            'checkboxOptions' => function ($model, $key, $index, $column) {
//                                $idUserProfileComunity = $this->params['idUserprofileCommunity'];
//                                return ['value' => $model->id,
//                                    'checked' => true,
//                                ];
//                            }
                        ],
                    ]

                ]);
                \yii\widgets\Pjax::end();
                ?>
            </div>


            <div class="col-xs-12 col-lg-6">
                <div class="col-lg-12">
                    <h3><?= AmosDocumenti::t('amosdocumenti', 'Gruppi')?></h3>
                </div>
                <?php echo \lispa\amos\core\views\AmosGridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        'name',
                        'description',
                        [
                            'attribute' => 'numberGroupMembers',
                            'label' => AmosDocumenti::t('amosdocumenti', '#number_group_members')
                        ],
                        [
                            'class' => '\kartik\grid\CheckboxColumn',
                            'rowSelectedClass' => \kartik\grid\GridView::TYPE_SUCCESS,
                            'name' => 'selection-groups'
                            // you may configure additional properties here
                        ],
                    ]

                ]); ?>
            </div>

            <div class="col-lg-12 col-sm-12">
                <?= $form->field($model, 'inviaNotifiche')->checkbox() ?>
            </div>

        <?php } ?>
        <?php $this->endBlock(); ?>

        <?php
        $itemsTab[] = [
            'label' => AmosDocumenti::tHtml('amosdocumenti', 'Email notifica'),
            'content' => $this->blocks['email-notification'],
            'options' => ['id' => 'tab-email-notification'],
        ];
    }
    ?>

    <?php
    $tabs = [
        'encodeLabels' => false,
        'items' => $itemsTab
    ];


    $tabs['hideReportTab'] = false;
    if(!$model->isNewRecord && (\Yii::$app->user->id != $model->created_by) && !\lispa\amos\community\utilities\CommunityUtil::isLoggedCommunityManager()){
        $tabs['hideReportTab'] = true;
    }

    if($isFolder /*|| $enableGroupNotification*/) {
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
        'urlClose' => $appController->getFormCloseUrl($model),
        'buttonNewSaveLabel' => (\Yii::t('amoscore', '#save_and_close')),
        'buttonSaveLabel' => \Yii::t('amoscore', '#save_and_close')

    ];
    $label = $appController->getFormCloseLabel($model);
    if ($label) {
        $config['closeButtonLabel'] = $label;
    }
    ?>
    <?= CloseSaveButtonWidget::widget($config) ?>
</div>
<?php ActiveForm::end(); ?>
