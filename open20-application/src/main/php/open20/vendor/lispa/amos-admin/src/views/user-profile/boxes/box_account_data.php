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
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

$js = "
$('#deactivate-account-btn').on('click', function(event) {
    event.preventDefault();
    var ok = confirm('" . AmosAdmin::t('amosadmin', 'Do you really want to deactivate your user') . '?' . "');
    if (ok) {
        window.location.href = $(this).attr('href');
    }
});
$('#reactivate-account-btn').on('click', function(event) {
    event.preventDefault();
    var ok = confirm('" . AmosAdmin::t('amosadmin', 'Do you really want to reactivate this user') . '?' . "');
    if (ok) {
        window.location.href = $(this).attr('href');
    }
});
";
$this->registerJs($js, View::POS_READY);

?>

<section>
    <h2>
        <?= AmosIcons::show('account') ?>
        <?= AmosAdmin::t('amosadmin', 'Account'); ?>
    </h2>
        <div class="col-xs-4 col-sm-6 nop">
    <?php if ($adminModule->confManager->isVisibleField('ultimo_accesso', ConfigurationManager::VIEW_TYPE_FORM)): ?>
        <?= Html::beginTag('dl') ?>
        <?= Html::tag('dt', AmosAdmin::t('amosadmin', 'Ultimo accesso:')) ?>
        <?= Html::tag('dd', ($model->ultimo_accesso ? Yii::$app->formatter->asDatetime($model->ultimo_accesso) : AmosAdmin::t('amosadmin', 'Nessun accesso'))) ?>
        <?= Html::endTag('dl') ?>
    <?php endif; ?>
    
    <?php if ($adminModule->confManager->isVisibleField('ultimo_logout', ConfigurationManager::VIEW_TYPE_FORM)): ?>
        <?= Html::beginTag('dl') ?>
        <?= Html::tag('dt', AmosAdmin::t('amosadmin', 'Ultimo logout:')) ?>
        <?= Html::tag('dd', ($model->ultimo_logout ? Yii::$app->formatter->asDatetime($model->ultimo_logout) : AmosAdmin::t('amosadmin', 'Nessun logout'))) ?>
        <?= Html::endTag('dl') ?>
    <?php endif; ?>
    
    <?php if ($adminModule->confManager->isVisibleField('attivo', ConfigurationManager::VIEW_TYPE_FORM)): ?>
        <?= Html::beginTag('dl') ?>
        <?= Html::tag('dt', AmosAdmin::t('amosadmin', 'Stato')) ?>
        <?= Html::tag('dd', ($model->attivo ? AmosAdmin::t('amosadmin', 'Active') : AmosAdmin::t('amosadmin', 'Deactivated'))) ?>
        <?= Html::endTag('dl') ?>
    <?php endif; ?>
        </div>

        <?php if(!(\Yii::$app->user->can('ADMIN') && $model->user_id == 1)){?>
            <?php if ($model->isActive() && Yii::$app->user->can('DeactivateAccount', ['model' => $model])): ?>
            <div class="col-xs-8 col-sm-6 text-right">
                <?= Html::a(AmosAdmin::t('amosadmin', 'Deactivate user'), ['/admin/user-profile/deactivate-account', 'id' => $model->id], [
                    'id' => 'deactivate-account-btn',
                    'class' => 'btn btn-danger-inverse',
                    'title' => AmosAdmin::t('amosadmin', 'Deactivate your user'),
    //                'data-confirm' => AmosAdmin::t('amosadmin', 'Do you really want to deactivate your user') . '?'
                ]) ?>
            </div>
            <?php endif; ?>
        <?php } ?>
    <?php if ($model->isDeactivated() && Yii::$app->user->can('ADMIN')): ?>
        <div class="col-md-6 col-sm-6">
            <?= Html::a(AmosAdmin::t('amosadmin', 'Reactivate this user'), ['/admin/user-profile/reactivate-account', 'id' => $model->id], [
                'id' => 'reactivate-account-btn',
                'class' => 'btn btn-navigation-primary',
                'title' => AmosAdmin::t('amosadmin', 'Reactivate this user'),
//                'data-confirm' => AmosAdmin::t('amosadmin', 'Do you really want to reactivate this user') . '?'
            ]) ?>
        </div>
    <?php endif; ?>
    <div class="clearfix"></div>
</section>
