<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile\boxes
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\admin\models\UserProfileAgeGroup;
use lispa\amos\core\forms\editors\Select;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 * @var string $idTabInsights
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

$js = "
$('#extended-presentation-link').click(function(event) {
    event.preventDefault();
    $('a[href=\"' + $(this).attr('href') + '\"]').tab('show');
});
";
$this->registerJs($js, View::POS_READY);

?>
<section>
    <div class="row">
        <?php if ($adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'nome')->textInput(['maxlength' => 255, 'readonly' => false]) ?>
            </div>
        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'cognome')->textInput(['maxlength' => 255, 'readonly' => false]) ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if (
        ($adminModule->confManager->isVisibleField('sesso', ConfigurationManager::VIEW_TYPE_FORM)) ||
        ($adminModule->confManager->isVisibleField('user_profile_age_group_id', ConfigurationManager::VIEW_TYPE_FORM))
    ): ?>
        <hr>
    <?php endif; ?>
    <div class="row">
        <?php if ($adminModule->confManager->isVisibleField('sesso', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($model, 'sesso', [
                    'template' => "{label}\n{hint}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}",
                ])->widget(Select::classname(), [
                    'options' => ['placeholder' => AmosAdmin::t('amosadmin', 'Select/Choose') . '...', 'disabled' => false],
                    'data' => [
                        'None' => AmosAdmin::t('amosadmin', 'Non Definito'),
                        'Maschio' => AmosAdmin::t('amosadmin', 'Maschio'),
                        'Femmina' => AmosAdmin::t('amosadmin', 'Femmina')
                    ]
                ])->label($model->getAttributeLabel('sesso') . ' ' . AmosIcons::show('lock', ['title' => AmosAdmin::t('amosadmin', '#confidential')])); ?>
            </div>
        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('user_profile_age_group_id', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($model, 'user_profile_age_group_id', [
                    'template' => "{label}\n{hint}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}",
                ])->widget(Select::classname(), [
                    'options' => ['placeholder' => AmosAdmin::t('amosadmin', 'Select/Choose') . '...', 'disabled' => false],
                    'data' => ArrayHelper::map(UserProfileAgeGroup::find()->orderBy(['id' => SORT_ASC])->asArray()->all(), 'id', 'age_group')
                ])->label($model->getAttributeLabel('age_group') . ' ' . AmosIcons::show('lock', ['title' => AmosAdmin::t('amosadmin', '#confidential')])); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($adminModule->confManager->isVisibleField('presentazione_breve', ConfigurationManager::VIEW_TYPE_FORM)): ?>
        <hr>
        <div class="row">
            <div class="col-xs-12">

                <?= $form->field($model, 'presentazione_breve')->limitedCharsTextArea([
                    'rows' => 6,
                    'readonly' => false,
                    'placeholder' => AmosAdmin::t('amosadmin', '#short_presentation_placeholder'),
                    'maxlength' => 140
                ])->label(AmosAdmin::t('amosadmin', '#short_presentation'), ['class' => 'bold']); ?>

                <?= Html::a(AmosAdmin::t('amosadmin', 'Do you want to include a more complete professional presentation') . '?', '#' . $idTabInsights, [
                    'data-toggle' => 'tab',
                    'class' => 'pull-right',
                    'id' => 'extended-presentation-link'
                ]) ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($adminModule->confManager->isVisibleField('note', ConfigurationManager::VIEW_TYPE_FORM)): ?>
        <hr>
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <?= $form->field($model, 'note')->textarea(['rows' => 6, 'readonly' => false, 'maxlength' => 500]) ?>
            </div>
        </div>
    <?php endif; ?>
</section>
