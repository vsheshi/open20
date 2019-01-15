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
use lispa\amos\core\icons\AmosIcons;

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

?>
<section>
    <h2>
        <?= AmosIcons::show('phone'); ?>
        <?= AmosAdmin::tHtml('amosadmin', 'Dati di Contatto'); ?>
    </h2>
    <div class="row">
        <?php if ($adminModule->confManager->isVisibleField('email', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($user, 'email')->textInput(['readonly' => false])
                    ->label($model->getAttributeLabel('email') . ' ' . AmosIcons::show('lock', ['title' => AmosAdmin::t('amosadmin', '#confidential')])) ?>
            </div>
        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('telefono', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($model, 'telefono')->textInput(['maxlength' => true, 'readonly' => false])
                    ->label($model->getAttributeLabel('telefono') . ' ' . AmosIcons::show('lock', ['title' => AmosAdmin::t('amosadmin', '#confidential')])) ?>
            </div>
        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('cellulare', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($model, 'cellulare')->textInput(['maxlength' => true, 'readonly' => false]) ?>
            </div>
        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('email_pec', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($model, 'email_pec')->textInput() ?>
            </div>
        <?php endif; ?>
    </div>
</section>
