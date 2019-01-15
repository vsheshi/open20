<?php

use lispa\amos\chat\AmosChat;
use lispa\amos\core\helpers\Html;
use yii\redactor\widgets\Redactor;

?>
<?= Html::beginForm('', 'post', [
    'id' => 'msg-form',
    'class' => 'col-xs-11 nop'
]); ?>
<label class="hidden" for="chat-message"><?= AmosChat::tHtml('amoschat', 'Messaggio') ?></label>
<?= Redactor::widget([
    'name' => 'text',
    'options' => [
        'id' => 'chat-message',
        'class' => 'form-control send-message',
        'placeholder' => AmosChat::t('amoschat', 'Scrivi una risposta...')
    ],
    'clientOptions' => [
        'focus' => true,
        'buttons' => Yii::$app->controller->module->formRedactorButtons,
        'lang' => substr(Yii::$app->language, 0, 2)
    ]
]) ?>
<?= Html::endForm(); ?>
