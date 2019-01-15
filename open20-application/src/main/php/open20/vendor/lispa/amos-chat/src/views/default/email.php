<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\chat
 * @category   CategoryName
 */

use lispa\amos\chat\AmosChat;
use lispa\amos\core\helpers\Html;

/**
 * @var \lispa\amos\admin\models\UserProfile $contactProfile
 */

?>

<div>
    <div style="box-sizing:border-box;">
        <div style="padding:5px 10px;background-color: #F2F2F2;text-align:center;">
            <h1 style="color:#000000;font-size:1.5em;margin:0;">
                <?= $contactProfile->getNomeCognome() . " " . AmosChat::t('amoschat', 'sent you a private message') ?>
            </h1>
        </div>
        <div style="border:1px solid #cccccc;padding:10px;margin-bottom:10px;background-color:#ffffff;margin-top:20px;text-align: center;">
            <h2 style="font-size:2em;line-height: 1;"><?= Html::a(
                    AmosChat::t('amoschat', "#mail_link_text"),
                    \Yii::$app->urlManager->createAbsoluteUrl('/messages/' . $contactProfile->user_id),
                    ['style' => 'color: #297A38;'])
                ?>
            </h2>
        </div>
    </div>
    <!--<div>
        <div style="margin-top:20px; display: flex; padding: 10px;">
            <div
                style="width: 50px; height: 50px; overflow: hidden;-webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%;float: left;">
                <?php /*echo \lispa\amos\admin\widgets\UserCardWidget::widget([
                    'model' => $contactProfile,
                    'onlyAvatar' => true,
                    'absoluteUrl' => true
                ]) ?>
            </div>
            <div style="margin: 15px 0 0 20px;">
                <?= AmosChat::t('amoschat', "#sent_by") ?> <span
                    style="font-weight: 900;"><?= $contactProfile->getNomeCognome() ?></span>
            </div>
        </div>
        <div style="width:100%;margin-top:30px">
            <p><?= AmosChat::t('amoschat', '#mail_footer_text') */?></p>
        </div>
    </div>-->
</div>


