<?php

use lispa\amos\core\helpers\Html;
use lispa\amos\core\forms\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\groups\models\Groups $model
 * @var yii\widgets\ActiveForm $form
 */

/* \lispa\amos\groups\assets\SpinnerWaitAsset::register($this); //TODO remove this asset and files */

$moduleL = \Yii::$app->getModule('layout');
if (!empty($moduleL)) {
    \lispa\amos\layout\assets\SpinnerWaitAsset::register($this);
} else {
    \lispa\amos\core\views\assets\SpinnerWaitAsset::register($this);
}

//TODO cercare una soluzione più elegante
$this->registerCss('#selected-members .select-on-check-all{ display: none; }');
$this->registerCss('#grid-members .select-on-check-all{ display: none; }');


$js = <<< JS
        $(document).ready(function(){
            setChecked();
            function setChecked() {
                var selected = [];
                $('#selected-members tbody tr').each(function() {
                    var user_id = $(this).find('input').val();
                        selected.push(user_id);          
                });

                $('#grid-members tbody tr').each(function() {
                    var valore = $(this).find('input').val();
                    var flag = 0;
                   
                    for(var i=0; i < selected.length; i++) {
                         if(selected[i] == valore ) {
                             $(this).find('input').attr('checked', true);
                             $(this).addClass('success');
                             flag = 1;        
                         }
                    }
                    
                    if(flag == 0) {
                         $(this).removeClass('success');
                        $(this).find('input').removeAttr('checked');
                    }
                });
            }
        
          $(document).on('click','#grid-members .kv-row-checkbox', function() {
                if(this.checked) {
                    var tr = $(this).closest('tr');
                    var new_tr = tr.clone();
                    new_tr.find('.kv-row-select input')
                        .attr('name', 'attrSelected[]')
                        .attr('hidden', 'hidden');
                 
                    if($('#selected-members tbody tr').find('div').attr('class')=='empty'){
                        $('#selected-members tbody tr').remove();
                    }
                    $('#selected-members tbody').append(new_tr);
                } 
                else {
                    var user_id = $(this).closest('tr').attr("data-key");
                    var a = $('#selected-members').find('[data-key="'+user_id+'"]');
                    $(a).remove();
                }

          });
          $.pjax.defaults.timeout = 5000;
          
        $(".search-button").click(function(event) {
            event.preventDefault();
            $('.loading').show();
            $.pjax.reload({
                container:'#pjax-container',
                method: 'get',
                data: $('#search-form-users input').serialize(),
                timeout: 5000
            }).done(function() {
                  $('.loading').hide();
             }); //Reload GridView
        });
        
        
         $("#sendForm").click(function(event) {
            $('#groups-form').submit();
        });
         
         $(document).on('pjax:end', function(data, status, xhr, options) {
             console.log('load');
            setChecked();
        });
         
         
    });
JS;

$this->registerJs($js);
$js2 = <<<JS
    $('.reset-search').click(function(){
        $('.search-users-index input').each(function(){
            $(this).val('');
            });
      return false;
    });
JS;
$this->registerJS($js2);

$usersArray = $model->users;
foreach ($usersArray as $user) {
    $model->attrGroupsMembers[] = $user->id;
}
$this->params['selection'] = $model->attrGroupsMembers;

$hiddenFields = [];
if (Yii::$app->hasModule('groups')) {
    $module = Yii::$app->getModule('groups');
    $hiddenFields = $module->hiddenFields;
}


$js = <<<JS
$('body').on('beforeValidate', '#groups-form', function (e) {
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
  }).on('afterValidate', '#groups-form', function (e) {
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
<div class="loading" id="loader" hidden></div>


<div class="groups-form col-xs-12 nop">
    <?php $this->beginBlock('generic'); ?>

    <?php $form = ActiveForm::begin([
        'options' => ['id' => 'groups-form'],
		'id' => 'groups-form'
    ]);
    $errorSummary = $form->errorSummary($model);
    echo $errorSummary;
    ?>
    <?php
    if (false && !$model->isNewRecord) {
        echo "<div class='col-lg-12'>";
        echo \lispa\amos\core\helpers\Html::a(\lispa\amos\groups\Module::t('amosgroups', 'Send mail to group'), '#', [
            'class' => 'btn btn-primary pull-right',
            'title' => \lispa\amos\groups\Module::t('app', 'New Email'),
            'data-target' => '#send-email', 'data-toggle' => 'modal'
        ], false);
        echo "</div>";

        \yii\bootstrap\Modal::begin([
            'header' => '<h3>' . \lispa\amos\groups\Module::t('app', 'New Email') . '</h3>',
            'id' => 'send-email',
//            'toggleButton' => ['label' => \lispa\amos\groups\Module::t('app', 'New Email'), 'class' => 'btn btn-primary'],
        ]);
        $grp = new \lispa\amos\groups\models\GroupsMailer();
        $grp->idGroup = $model->id;
        echo $this->render('_new_email', ['model' => $grp]);

        \yii\bootstrap\Modal::end();
    }
    ?>


    <div class="col-lg-12">
        <h2><?= Yii::t('amosgroups', '#choose_group_name_title') ?></h2>
        <p><?= Yii::t('amosgroups', '#choose_group_name_desc') ?></p>
        <div class="col-lg-6 col-sm-6 nop">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-12 col-sm-12 nop">
            <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>
        </div>

        <hr>
    </div>
    <?php ////--------------------------------------------------------------------------?>

    <div class="col-lg-12">
        <h2><?= Yii::t('amosgroups', '#select_users_title') ?></h2>
        <p><?= Yii::t('amosgroups', '#select_users_desc') ?></p>

        <div class="container-change-view">
            <div class="btn-tools-container">
                <div class="tools-right">
                    <span class="btn btn-tools-primary show-hide-element am am-search"
                          data-toggle-element="form-search"> </span>
                </div>
            </div>
        </div>
        <div class="search-users-index">
            <?php
            $modelSearch = new \lispa\amos\admin\models\search\UserProfileSearch();
            echo $this->render('_search_users', ['model' => $modelSearch, 'form' => $form]);
            ?>
        </div>


        <?php
        \yii\widgets\Pjax::begin(['id' => 'pjax-container', 'timeout' => 2000, 'clientOptions' => ['data-pjax-container' => 'grid-members']]); ?>
        <?php

        $columns = [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'name' => 'attrGroupsMembers',
                'rowSelectedClass' => \kartik\grid\GridView::TYPE_SUCCESS,
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if (in_array($model->id, (array)$this->params['selection'])) {
                        return ['value' => $model->id,
                            'checked' => true
                        ];
                    } else {
                        return ['value' => $model->id];
                    }
                }

            ],
            'nome',
            'cognome',
            'codice_fiscale',
            'email' => [
                'attribute' => 'user.email',
                'label' => 'Email'
            ],
        ];

        if (in_array('nome', $hiddenFields)){
            ArrayHelper::removeValue($columns, 'nome');
        }
        if (in_array('cognome', $hiddenFields)){
            ArrayHelper::removeValue($columns, 'cognome');
        }
        if (in_array('codice_fiscale', $hiddenFields)){
             ArrayHelper::removeValue($columns, 'codice_fiscale');
        }
        if (in_array('email', $hiddenFields)){
            ArrayHelper::removeValue($columns,'email');
        }

        echo \lispa\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProvider,
            'id' => 'grid-members',
            'columns' => $columns
        ]);
        \yii\widgets\Pjax::end();
        ?>
        <hr>
    </div>

    <?php
    if ($model->isNewRecord) {
        $dataProviderSelected = new \yii\data\ActiveDataProvider([
            'query' => \lispa\amos\admin\models\UserProfile::find()->andWhere(0)
        ]);
    } ?>
    <div class="col-lg-12">
        <h3><?= Yii::t('amosgroups', '#selected_users_title') ?></h3>
        <?php
        $columns = [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'rowSelectedClass' => \kartik\grid\GridView::TYPE_SUCCESS,
                'name' => 'attrSelected',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['value' => $model->id,
                        'checked' => true,
                        'hidden' => true
                    ];
                }
            ],
            'nome',
            'cognome',
            'codice_fiscale',
            'email' => [
                'attribute' => 'user.email',
                'label' => 'Email'
            ],
        ];

        if (in_array('nome', $hiddenFields)){
            ArrayHelper::removeValue($columns, 'nome');
        }
        if (in_array('cognome', $hiddenFields)){
            ArrayHelper::removeValue($columns, 'cognome');
        }
        if (in_array('codice_fiscale', $hiddenFields)){
            ArrayHelper::removeValue($columns, 'codice_fiscale');
        }
        if (in_array('email', $hiddenFields)){
            ArrayHelper::removeValue($columns,'email');
        }

        echo \lispa\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProviderSelected,
            'id' => 'selected-members',
            'columns' => $columns
        ]);
        ?>
    </div>
    <br>

    <div class="col-lg-12">
        <div class="pull-right">
            <?= Html::a(\lispa\amos\groups\Module::t('amosgroups', 'Annulla'), Url::previous(), ['class' => 'btn btn-secondary']) ?>
            <?= Html::button(\lispa\amos\groups\Module::t('amoscore', '#save_and_close'), ['id' => 'sendForm', 'class' => 'btn btn-primary btn-navigation-primary', 'type' => 'submit']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


    <div class="clearfix"></div>
    <?php $this->endBlock('generic'); ?>
    <?php $itemsTab[] = [
        'label' => Yii::t('cruds', 'generic'),
        'content' => $this->blocks['generic'],
    ];
    ?>

    <?= Tabs::widget(
        [
            'encodeLabels' => false,
            'items' => $itemsTab
        ]
    );
    ?>

</div>
