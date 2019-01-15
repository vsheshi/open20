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
use lispa\amos\myactivities\AmosMyActivities;

/** @var $model \lispa\amos\myactivities\basic\RequestToParticipateCommunityForManager */

$userProfile = UserProfile::find()->andWhere(['user_id' => $model->user_id])->one();
if (!empty($userProfile)):
    ?>
    <div class="wrap-activity">
        <div class="col-md-1 col-xs-2 icon-plugin">
            <?= AmosIcons::show('users', [], 'dash') ?>
        </div>
        <div class="col-md-3 col-xs-5 wrap-user">
            <?= UserCardWidget::widget(['model' => $userProfile]) ?>
            <span class="user"><?= $userProfile->getNomeCognome() ?></span>
            <br>
            <?= AmosAdmin::t('amosadmin', $userProfile->workflowStatus->label) ?>
        </div>
        <div class="col-md-5 col-xs-5 wrap-report">
            <div class="col-lg-12 col-xs-12">
                <strong><?= AmosMyActivities::t('amosmyactivities', 'Request for community participation'); ?></strong>
            </div>
            <div class="col-lg-12 col-xs-12">
                <?= Yii::$app->formatter->asDatetime($model->getUpdatedAt()) ?>
            </div>
            <div class="col-lg-12 col-xs-12">
                <p class="user-report"><?= $userProfile->getNomeCognome() ?></p>
                <?= AmosMyActivities::t('amosmyactivities',
                    ' asks you to be accepted as a community participant of your Community:'); ?>
                <?= $model->community->name; ?>
            </div>
            <div class="col-lg-12 col-xs-12">
                <?= Html::a(AmosIcons::show('search', [], 'dash') . ' ' . AmosMyActivities::t('amosmyactivities',
                        'View profile'),
                    Yii::$app->urlManager->createUrl([
                        '/admin/user-profile/view',
                        'id' => $userProfile->id
                    ])
                ) ?>
            </div>
        </div>
        <div class="col-md-3 col-xs-12 wrap-action">
            <?= Html::a(AmosIcons::show('check') . ' ' . AmosMyActivities::t('amosmyactivities', 'Validate'),
                Yii::$app->urlManager->createUrl([
                    '/community/community/accept-user',
                    'communityId' => $model->community_id,
                    'userId' => $model->user_id
                ]),
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(AmosIcons::show('close') . ' ' . AmosMyActivities::t('amosmyactivities', 'Reject'),
                Yii::$app->urlManager->createUrl([
                    '/community/community/reject-user',
                    'communityId' => $model->community_id,
                    'userId' => $model->user_id
                ]),
                ['class' => 'btn btn-secondary']
            ) ?>
        </div>
    </div>
    <hr>
    <?php
endif;
