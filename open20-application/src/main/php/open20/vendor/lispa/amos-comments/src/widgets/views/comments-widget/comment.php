<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comments\widgets\views\comments-widget
 * @category   CategoryName
 */

use lispa\amos\attachments\components\AttachmentsInput;
use lispa\amos\comments\AmosComments;
use lispa\amos\comments\assets\CommentsAsset;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\utilities\ModalUtility;
use yii\redactor\widgets\Redactor;
use yii\web\View;

CommentsAsset::register($this);

/**
 * @var \lispa\amos\comments\widgets\CommentsWidget $widget
 */

$js = "
$('#contribute-btn').on('click', function (event) {
    Comments.saveComment(" . $widget->model->id . ", '" . addslashes($widget->model->className()) . "')
});
";
$this->registerJs($js, View::POS_READY);

/** @var AmosComments $commentsModule */
$commentsModule = Yii::$app->getModule(AmosComments::getModuleName());

ModalUtility::createAlertModal([
    'id' => 'ajax-error-comment-modal-id',
    'modalDescriptionText' => AmosComments::t('amoscomments', '#AJAX_ERROR_COMMENT')
]);
ModalUtility::createAlertModal([
    'id' => 'empty-comment-modal-id',
    'modalDescriptionText' => AmosComments::t('amoscomments', '#EMPTY_COMMENT')
]);

?>

<div id="comments_contribute" class="contribute col-xs-12 nop">
    <?php if (Yii::$app->getUser()->can('COMMENT_CREATE', ['model' => $widget->model])) { ?>
        <h3><?= $widget->options['commentTitle'] ?></h3>
        <div id="bk-contribute" class="contribute-container">
            <div class="col-xs-12">
                <?= Html::label($widget->options['commentTitle'], 'contribute-area', ['class' => 'sr-only']) ?>
                <?= Redactor::widget([
                    'name' => 'contribute-area',
                    'value' => null,
                    'options' => [
                        'id' => 'contribute-area',
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        'placeholder' => $widget->options['commentPlaceholder'],
                        'lang' => substr(Yii::$app->language, 0, 2),
                        'buttonsHide' => [
                            'image',
                            'file'
                        ]
                    ],
                ]) ?>
            </div>
            <div class="col-lg-6 col-xs-12">
                <?= AttachmentsInput::widget([
                    'id' => 'commentAttachments',
                    'name' => 'commentAttachments',
                    'model' => $widget->model,
                    'options' => [ // Options of the Kartik's FileInput widget
                        'multiple' => true, // If you want to allow multiple upload, default to false
                    ],
                    'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                        'maxFileCount' => $commentsModule->maxCommentAttachments, // Client max files
                        'showPreview' => true
                    ]
                ]) ?>
            </div>
            <div class="col-lg-6 col-xs-12 text-right">
                <?= Html::button(AmosComments::t('amoscomments', '#COMMENT_BUTTON'), ['id' => 'contribute-btn',
                    'class' => 'btn btn-navigation-primary',
                    'title' => AmosComments::t('amoscomments', 'Comment content')]) ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <!--<div class="footer text-right">
            <?php /*echo Html::button(AmosComments::t('amoscomments', '#COMMENT_BUTTON'), ['id' => 'contribute-btn',
                'class' => 'btn btn-navigation-primary',
                'title' => AmosComments::t('amoscomments', 'Comment content')])*/ ?>
        </div>-->
    <?php } ?>
</div>
