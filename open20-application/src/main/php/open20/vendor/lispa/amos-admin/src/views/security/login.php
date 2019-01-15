<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\security
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\core\helpers\Html;
use lispa\amos\admin\assets\ModuleAdminAsset;
use lispa\amos\core\forms\ActiveForm;
use yii\helpers\ArrayHelper;

ModuleAdminAsset::register(Yii::$app->view);

/**
 * @var yii\web\View $this
 * @var yii\bootstrap\ActiveForm $form
 * @var \lispa\amos\admin\models\LoginForm $model
 */

$this->title = AmosAdmin::t('amosadmin', 'Login');
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var $socialAuthModule \lispa\amos\socialauth\Module
 */
$socialAuthModule = Yii::$app->getModule('socialauth');
?>

<div id="bk-formDefaultLogin" class="bk-loginContainer loginContainer">

    <div class="header col-xs-12">
        <?php if (!isset(Yii::$app->params['logo']) || !Yii::$app->params['logo']) : ?>
            <p class="welcome-message"><?= AmosAdmin::t('amosadmin', '#login_welcome_message') ?></p>
        <?php endif; ?>

        <?php if ($socialAuthModule && $socialAuthModule->enableLogin) : ?>
            <?= $this->render('parts/header', [
                'type' => 'login',
            ]); ?>
        <?php endif; ?>
    </div>

    <?php if ($socialAuthModule && $socialAuthModule->enableLogin) : ?>
        <div class="or-login col-xs-12 nop">
            <?= Html::tag('span', AmosAdmin::t('amosadmin', '#or')) ?>
        </div>
    <?php endif; ?>

    <div class="body col-xs-12 nop">
        <?= Html::tag('h2', AmosAdmin::t('amosadmin', '#title_login'), ['class' => 'title-login col-xs-12 nop']) ?>
        <?= Html::tag('h3', AmosAdmin::t('amosadmin', '#subtitle_login'), ['class' => 'subtitle-login col-xs-12 nop']) ?>
        <div class="row">
            <div class="col-lg-12 col-sm-12 nop">
                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?php if (\Yii::$app->params['template-amos']): ?>
                    <?= $form->field($model, 'ruolo')->dropDownList([
                        'ADMIN' => AmosAdmin::t('amosadmin', 'Admin'),
                        'VALIDATED_BASIC_USER' => AmosAdmin::t('amosadmin', 'Validated Basic User')
                    ]) ?>
                <?php endif; ?>
                <?php if (isset(\Yii::$app->params['isDemoLogin']) && \Yii::$app->params['isDemoLogin']): ?>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'username', ['inputOptions' => ['value' => 'demo']])->textInput(['readonly' => true])->label((AmosAdmin::t('amoscore', 'Username'))) ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'password', ['inputOptions' => ['value' => 'Demo1234']])->passwordInput(['readonly' => true]) ?>
                </div>
                <?php else: ?>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'username')->textInput()->label((AmosAdmin::t('amoscore', 'Username'))) ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <div class="forgot-password"><?= Html::a(AmosAdmin::t('amosadmin', '#forgot_password'), ['/admin/security/forgot-password'], ['title' => AmosAdmin::t('amosadmin', '#forgot_password_title_link'), 'target' => '_self']) ?></div>
                </div>
                <?php endif; ?>

            </div>

            <div class="col-xs-12">
                <?= $form->field($model, 'rememberMe')->checkbox()->label(AmosAdmin::t('amosadmin', '#remember_access'), ['class' => 'remember-me', 'title' => AmosAdmin::t('amosadmin', '#remember_access')]) ?>
            </div>

            <div class="col-xs-12">
                <?= Html::submitButton(AmosAdmin::t('amosadmin', '#text_button_login'), ['class' => 'btn btn-primary btn-administration-primary pull-right', 'name' => 'login-button', 'title' => AmosAdmin::t('amosadmin', '#text_button_login_title')]) ?>
                <?php ActiveForm::end(); ?>
            </div>

            <?php if (Yii::$app->getModule('admin')->enableRegister): ?>
                <div class="col-xs-12 footer-link">
                    <?= Html::tag('h3', AmosAdmin::t('amosadmin', '#new_user').
                        ' ' .
                        Html::a(AmosAdmin::t('amosadmin', '#register_now'), ['/admin/security/register'], ['class' => '', 'title' => AmosAdmin::t('amosadmin', '#register_now_title_link',['appName' => Yii::$app->name]), 'target' => '_self']),
                        ['class' => 'subtitle-login nom']) ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
