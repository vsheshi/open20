<?php

use lispa\amos\admin\models\UserProfile;
use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use yii\bootstrap\Modal;

/**
 * @var \lispa\amos\community\models\Community $model
 */

$communityModule = Yii::$app->getModule('community');
$fixedCommunityType = !is_null($communityModule->communityType);
$bypassWorkflow = $communityModule->bypassWorkflow;

$viewUrl = '/community/community/view?id=' . $model->id;






?>

<div class="card-container col-xs-12 nop">
    <?= ContextMenuWidget::widget([
        'model' => $model,
        'actionModify' => "/community/community/update?id=" . $model->id,
        'actionDelete' => "/community/community/delete?id=" . $model->id,
        'mainDivClasses' => '',
        'optionsDelete' => ['class' => 'delete-community-btn', 'data' => null,  'method' => 'post', 'pjax' => 0],
//        'optionsDelete' => ['class' => 'delete-community-btn'],  se si attiva il modale di bbostrap rimettere questa riga

    ]);
    ?>
    <div class="icon-header grow-pict">
        <div class="container-round-img">
            <?php
            $url = '/img/img_default.jpg';
            if (!is_null($model->communityLogo)) {
                $url = $model->communityLogo->getUrl('square_medium', false, true);
            }
            Yii::$app->imageUtility->methodGetImageUrl = 'getUrl';
            $roundImage = Yii::$app->imageUtility->getRoundImage($model->communityLogo);
            $logo = Html::img($url, [
                'class' => $roundImage['class'],
                'style' => "margin-left: " . $roundImage['margin-left'] . "%; margin-top: " . $roundImage['margin-top'] . "%;",
                'alt' => $model->getAttributeLabel('communityLogo')
            ]);
            ?>
            <?= Html::a($logo, $viewUrl, ['title' => $model->name]); ?>
        </div>
        <?= \lispa\amos\community\widgets\JoinCommunityWidget::widget(['model' => $model, 'divClassBtnContainer' => 'under-img']) ?>
    </div>
    <div class="icon-body">
        <?= $newsWidget = \lispa\amos\notificationmanager\forms\NewsWidget::widget([
            'model' => $model,
            'css_class' => 'badge badge-left'
        ]) ?>
        <h3>
            <?= Html::a($model->name, $viewUrl, ['title' => $model->name]); ?>
        </h3>
        <?php if(!$bypassWorkflow): ?>
            <?php
                if($model->validated_once){
                    $color = "grey";
                    if($model->status == Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED){
                        $color = "green";
                    }
                    echo Html::tag('div', AmosIcons::show('check-all', ['class' => 'am-2 ']), ['style' => 'color: '.$color]);
                }
            ?>
        <?php endif; ?>
        <?php if(!$fixedCommunityType): ?>
            <p><?= AmosCommunity::t('amoscommunity', 'Access type: ') . AmosCommunity::t('amoscommunity', $model->getCommunityTypeName()) ?></p>
        <?php endif; ?>
    </div>
</div>
