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

$this->title = AmosAdmin::t('amosadmin', 'Password dimenticata');
$this->params['breadcrumbs'][] = $this->title;

ModuleAdminAsset::register(Yii::$app->view);
?>

<div id="bk-formDefaultLogin" class="bk-loginContainer loginContainer">
    <div class="body col-xs-12 nop">
        <h2 class="title-login"><?= AmosAdmin::t('amosadmin', '#forgot_pwd_title'); ?></h2>
        <h3 class="subtitle-login"><?= AmosAdmin::t('amosadmin', '#forgot_pwd_subtitle'); ?></h3>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <div class="row">
            <div class="col-xs-12">
                <?= $form->field($model, 'email') ?>
            </div>
            <div class="col-xs-12 footer-link">
                <?= Html::submitButton(AmosAdmin::t('amosadmin', '#forgot_pwd_send'), ['class' => 'btn btn-primary btn-administration-primary pull-right', 'name' => 'login-button', 'title' => AmosAdmin::t('amosadmin', '#forgot_pwd_send_title')]) ?>
                <?= Html::a(AmosAdmin::t('amosadmin', '#go_to_login'), ['/admin/security/login'], ['class' => 'btn btn-secondary pull-left', 'title' => AmosAdmin::t('amosadmin', '#go_to_login_title')]) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>


</div>
