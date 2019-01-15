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

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 * @var bool $spediscicredenzialienable
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

?>
<section class="m-t-30">
    <h2>
        <?= AmosIcons::show('lock') ?>
        <?= AmosAdmin::tHtml('amosadmin', 'Dati di Accesso') ?>
    </h2>
    <?php if ($adminModule->confManager->isVisibleField('username', ConfigurationManager::VIEW_TYPE_FORM)): ?>
        <div class="col-xs-4 col-sm-6 nop">
            <?= Html::beginTag('dl', ['class' => 'field-user-username']) ?>
            <?= Html::tag('dt', $user->getAttributeLabel('username')) ?>
            <?= Html::tag('dd', $user->username ? $user->username : AmosAdmin::t('amosadmin', 'Non ancora definito')) ?>
            <?= Html::endTag('dl') ?>
        </div>
    <?php endif; ?>

    <div id="user-password" class="col-xs-8 col-sm-6 text-right pull-right">
        <div id="form-credenziali" class="bk-form-credenziali">
            <?php // if (!$model->isNewRecord && isset($user['email']) && strlen(trim($user['email']))):
            //if($spediscicredenzialienable) {
            ?>
            <?php if (!$model->isNewRecord && isset($user['email']) && strlen(trim($user['email']))): ?>
                <?php if (Yii::$app->getUser()->can("GESTIONE_UTENTI")): ?>
                    <?php if ($spediscicredenzialienable): ?>
                        <?= Html::a(
                            AmosIcons::show('email') . AmosAdmin::t('amosadmin', 'Spedisci credenziali'),
                            [
                                '/admin/security/admin-send-credentials',
                                'id' => $model->id
                            ],
                            [
                                'class' => 'btn btn-action-primary btn-spedisci-credenziali ',
                                'title' => AmosAdmin::t('amosadmin', 'Permette l\'invio di una mail contenente un link temporale per modificare le proprie credenziali di accesso.'),
                                'data-confirm' => AmosAdmin::t('amosadmin', 'Sei sicuro di voler inviare le credenziali? Sarà inviata una mail contenente un link per modificare le credenziali. Vuoi continuare?')
                            ]); ?>
                    <?php else: ?>
                        <div id="info-spedisci" class="btn btn-action-primary disabled" data-toggle="tooltip" data-placement="left"
                             title="<?= AmosAdmin::t('amosadmin', 'Per spedire le credenziali occorre impostare il Ruolo nella sezione AMMINISTRAZIONE'); ?>">
                            <?= AmosAdmin::t('amosadmin', 'Spedisci credenziali'); ?>
                        </div>
                        <div class=""><?= AmosAdmin::tHtml('amosadmin', 'Per spedire le credenziali occorre impostare il Ruolo nella sezione AMMINISTRAZIONE') ?></div>
                        <div class="btn btn-action-primary disabled"><?= AmosAdmin::t('amosadmin', 'Spedisci credenziali'); ?></div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php
                /** @var \lispa\amos\core\user\User $identity */
                $identity = Yii::$app->user->identity
                ?>
                <?php if ($user['id'] == $identity->id): ?>
                    <?= Html::a(AmosIcons::show('unlock') . AmosAdmin::t('amosadmin', 'Cambia password'), ['/admin/user-profile/cambia-password', 'id' => $model->id], [
                        'class' => 'btn  btn-action-primary btn-cambia-password'
                    ]); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="clearfix"></div>
</section>
