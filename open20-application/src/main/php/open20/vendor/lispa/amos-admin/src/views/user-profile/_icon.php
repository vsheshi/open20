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
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\admin\models\UserProfile $model
 */

$userId = $model->user_id;
/** @var \lispa\amos\admin\controllers\UserProfileController $appController */
$appController = Yii::$app->controller;
$appController->setCwhScopeNetworkInfo($userId);

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

$nomeCognome = '';
if ($adminModule->confManager->isVisibleBox('box_informazioni_base', ConfigurationManager::VIEW_TYPE_VIEW)) {
    if ($adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_VIEW)) {
        $nomeCognome .= $model->nome;
    }
    if ($adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_VIEW)) {
        $nomeCognome .= ' ' . $model->cognome;
    }
}

$viewUrl = "/admin/user-profile/view?id=" . $model->id;
$enableUserContacts = AmosAdmin::getInstance()->enableUserContacts;

$prevalentPartnershipTruncated = '';
$prevalentPartnershipName = '';
if (!is_null($model->prevalentPartnership)) {
    $prevalentPartnershipTruncated = $model->prevalentPartnership;
    $prevalentPartnershipName = $model->prevalentPartnership->name;
}

?>

<div class="card-container col-xs-12 nop">
    <?= ContextMenuWidget::widget([
        'model' => $model,
        'actionModify' => '/admin/user-profile/update?id=' . $model->id,
        'disableDelete' => true
    ]) ?>
    <?php if (($adminModule->confManager->isVisibleBox('box_foto', ConfigurationManager::VIEW_TYPE_VIEW)) &&
        ($adminModule->confManager->isVisibleField('userProfileImage', ConfigurationManager::VIEW_TYPE_VIEW))
    ): ?>
        <div class="icon-header grow-pict">
            <div class="container-round-img">
                <?php
                $url = $model->getAvatarUrl('square_small');
                Yii::$app->imageUtility->methodGetImageUrl = 'getAvatarUrl';
                $logoOptions = [
                    'class' => Yii::$app->imageUtility->getRoundImage($model)['class'],
                    'style' => "margin-left: " . Yii::$app->imageUtility->getRoundImage($model)['margin-left'] . "%; margin-top: " . Yii::$app->imageUtility->getRoundImage($model)['margin-top'] . "%;",
                    'alt' => $model->getNomeCognome(),
                ];
                $options = [];
                if (strlen($nomeCognome) > 0) {
                    $logoOptions['alt'] = $nomeCognome;
                    $options['title'] = $nomeCognome;
                }
                $logo = Html::img($url, $logoOptions);
                ?>
                <?= Html::a($logo, $viewUrl, $options); ?>
            </div>
            <?php if ($enableUserContacts && Yii::$app->user->id != $model->user_id): ?>
                <?= \lispa\amos\admin\widgets\ConnectToUserWidget::widget(['model' => $model, 'divClassBtnContainer' => 'under-img']) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="icon-body">
        <?= \lispa\amos\notificationmanager\forms\NewsWidget::widget([
            'model' => $model,
            'css_class' => 'badge badge-left'
        ]); ?>
        <h3>
            <?= Html::a($model->getNomeCognome(), $viewUrl, ['title' => $model->getNomeCognome()]); ?>
        </h3>
        <?php
        if ($model->validato_almeno_una_volta) {
            $icons = '';
            $color = "grey";
            $title = AmosAdmin::t('amosadmin', 'Profile Active');
            if ($model->status == \lispa\amos\admin\models\UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED) {
                $color = "green";
                $title = AmosAdmin::t('amosadmin', 'Profile Validated');
            }
            if (!$adminModule->bypassWorkflow) {
                // TODO replace check-all with cockade
                $statusIcon = AmosIcons::show('check-all', ['class' => 'am-2 ', 'style' => 'color: ' . $color, 'title' => $title]);
                $icons .= $statusIcon;
            }

            if (
                ($adminModule->confManager->isVisibleBox('box_facilitatori', ConfigurationManager::VIEW_TYPE_VIEW)) &&
                ($adminModule->confManager->isVisibleField('facilitatore_id', ConfigurationManager::VIEW_TYPE_VIEW))
            ) {
                $facilitatorUserIds = Yii::$app->getAuthManager()->getUserIdsByRole("FACILITATOR");
                if (in_array($model->user_id, $facilitatorUserIds)) {
                    //TODO replace account with man dressing tie and jacket
                    $facilitatorIcon = AmosIcons::show('account', [
                        'class' => 'am-2',
                        'style' => 'color: green',
                        'title' => AmosAdmin::t('amosadmin', 'Facilitator')
                    ]);
                    $icons .= $facilitatorIcon;
                }
            }

            echo Html::tag('div', $icons);
        }
        ?>

        <?php
        if (isset($this->params['role'])) {
            $role = $this->params['role'];
            echo Html::tag('p', AmosAdmin::t('amosadmin', $role));
        }
        if (isset($this->params['status'])) {
            $status = $this->params['status'];
            echo Html::tag('p', AmosAdmin::t('amosadmin', 'Status:') . ' ' . AmosAdmin::t('amosadmin', $status));
        }
        ?>
        <?php if (
            ($adminModule->confManager->isVisibleBox('box_prevalent_partnership', ConfigurationManager::VIEW_TYPE_VIEW)) &&
            ($adminModule->confManager->isVisibleField('prevalent_partnership_id', ConfigurationManager::VIEW_TYPE_VIEW))
        ): ?>
            <div data-toggle="tooltip" title="<?= $prevalentPartnershipName; ?>">
                <?= $prevalentPartnershipTruncated; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
