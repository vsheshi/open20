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
use lispa\amos\socialauth\models\SocialAuthUsers;

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

/**
 * @var $socialAuthModule \lispa\amos\socialauth\Module
 */
$socialAuthModule = Yii::$app->getModule('socialauth');

if ($socialAuthModule) :
    ?>

    <section>
        <h2>
            <?= AmosIcons::show('settings') ?>
            <?= AmosAdmin::tHtml('amosadmin', 'Access with social account') ?>
        </h2>
        <p><?= AmosAdmin::t('amosadmin', 'You can link your social accounts and then access the Open Innovation Platform with any of these accounts') . '.' ?></p>
        <div class="col-xs-12 m-t-15 nop">
            <?php foreach ($socialAuthModule->providers as $name => $config) : ?>
                <?php if ($adminModule->confManager->isVisibleField(strtolower($name), ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <div class="col-xs-6 col-sm-3 text-center nop">
                        <?php
                        $alreadyLinkedSocial = SocialAuthUsers::findOne([
                            'user_id' => $user->id,
                            'provider' => strtolower($name)
                        ]);
                        ?>
                        <?php if ($alreadyLinkedSocial && $alreadyLinkedSocial->id): ?>
                            <?= Html::a(
                                AmosIcons::show(strtolower($name)) . AmosAdmin::t('amosadmin', 'Disconnect'),
                                Yii::$app->urlManager->createAbsoluteUrl('/socialauth/social-auth/unlink-user?provider=' . strtolower($name)),
                                [
                                    'id' => 'contribute-btn',
                                    'class' => 'btn btn-danger-inverse',
                                    'title' => AmosAdmin::t('amosadmin', 'Disconnect from your account')
                                ]) ?>
                        <?php else: ?>
                            <?= Html::a(
                                AmosIcons::show(strtolower($name)) . AmosAdmin::t('amosadmin', 'Connect'),
                                Yii::$app->urlManager->createAbsoluteUrl('/socialauth/social-auth/link-user?provider=' . strtolower($name)),
                                [
                                    'id' => 'contribute-btn',
                                    'class' => 'btn btn-navigation-primary',
                                    'title' => AmosAdmin::t('amosadmin', 'Connect with your account')
                                ]) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="clearfix"></div>
    </section>
    <?php
endif;
?>