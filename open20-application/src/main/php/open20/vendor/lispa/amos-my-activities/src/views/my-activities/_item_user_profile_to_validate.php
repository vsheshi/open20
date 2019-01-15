<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\views\my-activities
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\admin\widgets\UserCardWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\utilities\ModalUtility;
use lispa\amos\myactivities\AmosMyActivities;

/** @var $model \lispa\amos\myactivities\basic\WaitingContacts */


?>
<div class="wrap-activity">
    <div class="col-md-1 col-xs-2 icon-plugin">
        <?= AmosIcons::show('users', [], 'dash') ?>
    </div>
    <?= \lispa\amos\myactivities\widgets\UserRequestValidation::widget([
        'model' => $model,
        'labelKey' => 'User validation request'
    ]) ?>
    <div class="col-md-3 col-xs-12 wrap-action">
        <?= ModalUtility::addConfirmRejectWithModal([
            'modalId' => 'validate-user-profile-modal-id-' . $model->id,
            'modalDescriptionText' => AmosMyActivities::t('amosmyactivities', '#VALIDATE_USER_PROFILE_MODAL_TEXT'),
            'btnText' => AmosIcons::show('check') . ' ' . AmosMyActivities::t('amosmyactivities', 'Validate'),
            'btnLink' => Yii::$app->urlManager->createUrl([
                '/admin/user-profile/validate-user-profile',
                'id' => $model->id
            ]),
            'btnOptions' => [
                'class' => 'btn btn-primary'
            ]
        ]); ?>
        <?= ModalUtility::addConfirmRejectWithModal([
            'modalId' => 'reject-user-profile-modal-id-' . $model->id,
            'modalDescriptionText' => AmosMyActivities::t('amosmyactivities', '#REJECT_USER_PROFILE_MODAL_TEXT'),
            'btnText' => AmosIcons::show('close') . ' ' . AmosMyActivities::t('amosmyactivities', 'Reject'),
            'btnLink' => Yii::$app->urlManager->createUrl([
                '/admin/user-profile/reject-user-profile',
                'id' => $model->id
            ]),
            'btnOptions' => [
                'class' => 'btn btn-secondary'
            ]
        ]); ?>
    </div>
</div>
<hr>
