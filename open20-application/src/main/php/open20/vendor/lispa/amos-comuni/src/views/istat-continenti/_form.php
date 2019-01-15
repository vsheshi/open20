<?php

use lispa\amos\core\helpers\Html;
use lispa\amos\core\forms\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use lispa\amos\comuni\AmosComuni;

/**
 * @var yii\web\View $this
 * @var lispa\amos\comuni\models\IstatContinenti $model
 * @var yii\widgets\ActiveForm $form
 */


?>

<div class="istat-continenti-form col-xs-12 nop">

    <?php $form = ActiveForm::begin(); ?>




    <?php $this->beginBlock('principale'); ?>

    <div class="col-lg-6 col-sm-6">

        <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="clearfix"></div>
    <?php $this->endBlock('principale'); ?>

    <?php $itemsTab[] = [
        'label' => AmosComuni::t('amoscomuni', 'Principale '),
        'content' => $this->blocks['principale'],
    ];
    ?>

    <?= Tabs::widget(
        [
            'encodeLabels' => false,
            'items' => $itemsTab
        ]
    );
    ?>
    <?= CloseSaveButtonWidget::widget(['model' => $model]); ?>
    <?php ActiveForm::end(); ?>
</div>
