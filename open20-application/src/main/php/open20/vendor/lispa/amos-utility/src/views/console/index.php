<!DOCTYPE html>
<?php
    use \yii\helpers\Html;
    use lispa\amos\core\forms\CloseButtonWidget;

?>

 <div class="">
    <h1>Platform Console Run</h1>

    
    <?php $form = \lispa\amos\core\forms\ActiveForm::begin(
            ['action' => 'run-cmd', 'id' => 'forum_post', 'method' => 'get',]
            ) ?>
    <div class="col-sm-6">
        <h3>
        <?= Html::label('Console Command', 'cmd_text'); ?>
        </h3>
        <?= Html::textarea('cmd','',['rows' => '6', 'cols' => '50', 'id' => 'cmd_text']);?>
    </div>

    <hr>
    <div class="col-sm-12 ">
       <?= Html::submitButton('Run', ['class' => 'btn btn-navigation-primary', 'name' => 'submit-button']) ?>
       <?= CloseButtonWidget::widget(['urlClose' => '/utility/']) ?>
    </div>
    <?php \lispa\amos\core\forms\ActiveForm::end() ?>

 </div>
    