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
use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\admin\utility\UserProfileUtility;
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\user\User;
use openinnovation\organizations\widgets\OrganizationsCardWidget;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\admin\models\UserProfile $model
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->getModule(AmosAdmin::getModuleName());

if ($model instanceof User) {
    $model = $model->getProfile();
}

$nomeCognome = '';
if ($adminModule->confManager->isVisibleBox('box_informazioni_base', ConfigurationManager::VIEW_TYPE_VIEW)) {
    if ($adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_VIEW)) {
        $nomeCognome .= $model->nome;
    }
    if ($adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_VIEW)) {
        $nomeCognome .= ' ' . $model->cognome;
    }
}

$isVisibleEmail = (($adminModule->confManager->isVisibleBox('box_informazioni_base', ConfigurationManager::VIEW_TYPE_VIEW)) &&
    ($adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_VIEW)) &&
    ($adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_VIEW)) &&
    $model->user->email && Yii::$app->user->can('ADMIN'));

$confirmText = AmosAdmin::t('amosadmin', 'You have selected') . " ".  $model->getNomeCognome() ." " . AmosAdmin::t('amosadmin', 'as your facilitator. To confirm click on the CONFIRM button. At the confirm the facilitator will be bound to the user profile.');

$isAssociateFacilitator = (Yii::$app->controller->action->id == 'associate-facilitator');
if ($isAssociateFacilitator) {
    \lispa\amos\core\utilities\ModalUtility::createConfirmModal([
        'id' => 'confirmPopup' . $model->id,
        'modalDescriptionText' => $confirmText,
        'confirmBtnOptions' => ['class' => 'btn btn-primary confirmBtn', 'data' => ['model_id' => $model->user_id]],
    ]);
}
?>

<div class="list-horizontal-element col-xs-12 nop">

    <div class="list-element-left">
        <?php if (($adminModule->confManager->isVisibleBox('box_foto', ConfigurationManager::VIEW_TYPE_VIEW)) &&
            ($adminModule->confManager->isVisibleField('userProfileImage', ConfigurationManager::VIEW_TYPE_VIEW))
        ): ?>
            <?php
            Yii::$app->imageUtility->methodGetImageUrl = "getAvatarUrl";
            $logo = Html::tag('div', Html::img($model->getAvatarUrl('square_small'), [
                'class' => Yii::$app->imageUtility->getRoundImage($model)['class'],
                'style' => "margin-left: " . Yii::$app->imageUtility->getRoundImage($model)['margin-left'] . "%; margin-top: " . Yii::$app->imageUtility->getRoundImage($model)['margin-top'] . "%;",
                'alt' => $model->getNomeCognome(),
                'title' => $model->getNomeCognome(),
            ]),
                ['class' => 'container-round-img-lg']);
            ?>
            <div class="grow-pict">
                <?php if ($isAssociateFacilitator): ?>
                    <?= $logo ?>
                <?php else: ?>
                    <?= Html::a($logo, ['/admin/user-profile/view', 'id' => $model->id]); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="list-element-right">
        <?php if (!$isAssociateFacilitator): ?>
            <?= ContextMenuWidget::widget([
                'model' => $model,
                'mainDivClasses' => 'pull-right',
                'actionModify' => '/admin/user-profile/update?id=' . $model->id,
                'disableDelete' => true
            ]) ?>
        <?php endif; ?>
        <div class="list-element-body">
            <?php if (($adminModule->confManager->isVisibleBox('box_informazioni_base', ConfigurationManager::VIEW_TYPE_VIEW)) &&
                ($adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_VIEW)) &&
                ($adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_VIEW))
            ): ?>
                <h3>
                    <?php if ($isAssociateFacilitator): ?>
                        <?= $nomeCognome ?>
                    <?php else: ?>
                        <?= Html::a($nomeCognome, ['/admin/user-profile/view', 'id' => $model->id]); ?>
                    <?php endif; ?>
                </h3>
            <?php endif; ?>
            <?php if ($isVisibleEmail): ?>
                <p>
                    <?= AmosIcons::show('email'); ?>
                    <span><?= $model->user->email; ?></span>
                </p>
            <?php endif; ?>
            <?php if (($adminModule->confManager->isVisibleBox('box_informazioni_base', ConfigurationManager::VIEW_TYPE_VIEW)) &&
                ($adminModule->confManager->isVisibleField('presentazione_breve', ConfigurationManager::VIEW_TYPE_VIEW))
            ): ?>
                <br>
                <p>
                    <span><?= ($model->presentazione_breve ? $model->presentazione_breve : ''); ?></span>
                </p>
            <?php endif; ?>
            <?php if (($adminModule->confManager->isVisibleBox('box_presentazione_personale', ConfigurationManager::VIEW_TYPE_VIEW)) &&
                ($adminModule->confManager->isVisibleField('presentazione_personale', ConfigurationManager::VIEW_TYPE_VIEW))
            ): ?>
                <br>
                <p>
                    <span><?= ($model->presentazione_personale ? $model->presentazione_personale : ''); ?></span>
                </p>
            <?php endif; ?>
            <?php if (($adminModule->confManager->isVisibleBox('box_prevalent_partnership', ConfigurationManager::VIEW_TYPE_VIEW)) &&
                ($adminModule->confManager->isVisibleField('prevalent_partnership_id', ConfigurationManager::VIEW_TYPE_VIEW)) &&
                ((Yii::$app->controller->action->id == 'facilitator-users') || $isAssociateFacilitator)
            ): ?>
                <?php if (!is_null($model->prevalentPartnership)): ?>
                    <br>
                    <div class="text-uppercase bold"><?= AmosAdmin::t('amosadmin', 'Prevalent partnership') . ':' ?></div>
                    <p>
                        <?php echo OrganizationsCardWidget::widget(['model' => $model->prevalentPartnership]);?>
                    </p>
                <?php endif; ?>
                <?php if ($isAssociateFacilitator): ?>
                    <?= Html::a(
                        AmosIcons::show('square-check', ['class' => 'm-r-5'], 'dash') . AmosAdmin::t('amosadmin', 'Set as facilitator'),
                        null,
                        [
                            'class' => 'btn btn-navigation-primary set-facilitator-btn',
                            'data-model_id' => $model->id, //TODO check if it was user_id instead...
                            'data-model_name' => $model->nome,
                            'data-model_surname' => $model->cognome,
                            'data-target' => '#confirmPopup' . $model->id,
                            'data-toggle' => 'modal',
//                            'data-confirm' => $confirmText,
//                            'data-pjax' => 0
                        ]
                    ); ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php $communityModule = Yii::$app->getModule('community'); ?>
            <?php if (!is_null($communityModule) && (Yii::$app->controller->action->id == 'community-manager-users')): ?>
                <?php /** @var \lispa\amos\community\AmosCommunity $communityModule */ ?>
                <?php $userCommunities = UserProfileUtility::getCommunitiesForManagers($communityModule, $model->user_id); ?>
                <?php if (count($userCommunities)): ?>
                    <br>
                    <div>
                        <span class="text-uppercase bold"><?= AmosAdmin::t('amosadmin', 'Community manager for') . ':' ?></span>
                        <?php foreach ($userCommunities as $userCommunity): ?>
                            <?php /** @var \lispa\amos\community\models\Community $userCommunity */ ?>
                            <div><?= $userCommunity->name ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
