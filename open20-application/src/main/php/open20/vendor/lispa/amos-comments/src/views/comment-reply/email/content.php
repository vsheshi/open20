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
    <div style="color:#000000;">
        <h2 style="font-size:2em;line-height: 1;margin:0;padding:10px 0;">
            <?= Html::a($contextModel->getTitle(), \Yii::$app->urlManager->createAbsoluteUrl($contextModel->getFullViewUrl()) . "#comments_anchor", ['style' => 'color: green;']) ?>
        </h2>
    </div>

    <div style="box-sizing:border-box;font-size:13px;font-weight:normal;">
        <?php
        echo $contextModel->getDescription(true);
        ?>
    </div>
    <div style="box-sizing:border-box;padding-bottom: 5px;color:#000000;">
        <div style="margin-top:20px;">
            <div style="display: flex;width: 100%;">
                <div
                        style="width: 50px; height: 50px; overflow: hidden;-webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%;float: left;">
                    <?php
                    $layout = '{publisher}';
                    if ($model instanceof ModelContentInterface) {
                        $layout = '{publisher}{publishingRules}{targetAdv}';
                    }
                    ?>
                    <?php if ($model->getCreatedUserProfile() != null): ?>
                        <?= \lispa\amos\admin\widgets\UserCardWidget::widget([
                            'model' => $model->getCreatedUserProfile()->one(),
                            'onlyAvatar' => true,
                            'absoluteUrl' => true
                        ])
                        ?>
                    <?php endif; ?>
                </div>

                <div style="margin-left: 20px;">
                    <?= \lispa\amos\core\forms\PublishedByWidget::widget([
                        'model' => $model,
                        'modelContext' => $contextModel,
                        'layout' => $layout,
                    ]) ?>
                    <span style="font-weight:normal;"><?php echo $model->comment_text ?></span>

                    <?php
                    if ($model_reply) {
                        ?>
                        <div style="box-sizing:border-box;padding-bottom: 5px;">
                            <div style="margin-top:20px;">
                                <div style="display: flex;width: 100%;">
                                    <div
                                            style="width: 50px; height: 50px; overflow: hidden;-webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%;float: left;">
                                        <?php
                                        $layout = '{publisher}';
                                        if ($model_reply instanceof ModelContentInterface) {
                                            $layout = '{publisher}{publishingRules}{targetAdv}';
                                        }
                                        ?>
                                        <?php if ($model_reply->getCreatedUserProfile() != null): ?>
                                            <?= \lispa\amos\admin\widgets\UserCardWidget::widget([
                                                'model' => $model_reply->getCreatedUserProfile()->one(),
                                                'onlyAvatar' => true,
                                                'absoluteUrl' => true
                                            ])
                                            ?>
                                        <?php endif; ?>
                                    </div>

                                    <div style="margin-left: 20px;">
                                        <?= \lispa\amos\core\forms\PublishedByWidget::widget([
                                            'model' => $model_reply,
                                            'layout' => $layout,
                                        ]) ?>
                                        <span
                                                style="font-weight:normal;"><p><?php echo $model_reply->comment_reply_text ?></p></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
