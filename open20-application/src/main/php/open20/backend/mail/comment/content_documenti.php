<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comments\views\comment\email
 * @category   CategoryName
 */

use lispa\amos\core\helpers\Html;
use lispa\amos\cwh\base\ModelContentInterface;

?>
<div style="border:1px solid #cccccc;padding:10px;margin-bottom: 10px;background-color: #ffffff;margin-top:20px">

    <div style="box-sizing:border-box;font-size:13px;font-weight:normal;">
        <?php if (!empty($user)): ?>
            <p><?= \Yii::t('app', 'Gentile <strong>'. $user->userProfile->getNomeCognome() . '</strong>')?></p>
        <?php endif; ?>

        <?php
        $communityName = '';
        $cwh = Yii::$app->getModule("cwh");
        $community = Yii::$app->getModule("community");
        if (isset($cwh) && isset($community)) {
            $cwh->setCwhScopeFromSession();
            if (!empty($cwh->userEntityRelationTable)) {
                $entityId = $cwh->userEntityRelationTable['entity_id'];
                $community = \lispa\amos\community\models\Community::findOne($entityId);
                if(!empty($community)) {
                    $communityName = '<strong>' . $community->name. '</strong>';
                }
            }
        }?>
        <?php if (!empty($model_reply)){ ?>
            <p><strong><?=$model_reply->getCreatedUserProfile()->one()->getNomeCognome(). '</strong>' . \Yii::t('app', ' ha risposto ad un commento su un documento dell’Area di Lavoro ') . $communityName ?></p>
        <?php } elseif ($model->getCreatedUserProfile() != null){?>
            <p><strong><?=$model->getCreatedUserProfile()->one()->getNomeCognome(). '</strong>' . \Yii::t('app', ' ha aggiunto un commento ad un documento dell’Area di Lavoro ') . $communityName?></p>
        <?php }; ?>
        <p><strong>Documento: </strong><?= $contextModel->getTitle()?></p>
        <strong>Commento: </strong>
            <i><?php echo $model->comment_text ?></i>
        <?php
        if ($model_reply) {
            ?>
            <strong>Risposta a commento: </strong>
            <i><?php echo $model_reply->comment_reply_text ?></i>
            <?php
        }
        ?>
        <p><?= Html::a(\Yii::t('app', 'Rispondi al commento'), \Yii::$app->urlManager->createAbsoluteUrl($contextModel->getFullViewUrl()) . "#comments_anchor", ['style' => 'color: green;']) ?></p>
        <p>Grazie,<br>
            lo Staff PCDoc</p>
    </div>
</div>
