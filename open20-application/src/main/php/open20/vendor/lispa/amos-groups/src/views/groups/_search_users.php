<?php

use lispa\amos\core\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\groups\models\search\GroupsSearch $model
 * @var yii\widgets\ActiveForm $form
 */


$hiddenFields = [];
if (Yii::$app->hasModule('groups')) {
    $module = Yii::$app->getModule('groups');
    $hiddenFields = $module->hiddenFields;
}
?>

<div id="search-form-users" data-toggle-element="form-search" class="element-to-toggle">
    <div class="col-xs-12"><h3>Cerca per:</h3></div>


    <?php
    if (!in_array('nome', $hiddenFields)):
        ?>
        <div class="col-sm-6 col-lg-3">
            <?= $form->field($model, 'nome') ?>
        </div>
    <?php
    endif;
    ?>


    <?php
    if (!in_array('cognome', $hiddenFields)):
        ?>
        <div class="col-sm-6 col-lg-3">
            <?= $form->field($model, 'cognome') ?>
        </div>
    <?php
    endif;
    ?>


<!--    --><?php
//    if (!in_array('codice_fiscale', $hiddenFields)):
//        ?>
<!--        <div class="col-sm-6 col-lg-3">-->
<!--            --><?php //echo  $form->field($model, 'codice_fiscale') ?>
<!--        </div>-->
<!--    --><?php
//    endif;
//    ?>


    <?php
    if (!in_array('email', $hiddenFields)):
        ?>
        <div class="col-sm-6 col-lg-3">
            <?= $form->field($model, 'email') ?>
        </div>
    <?php
    endif;
    ?>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::a(Yii::t('amosgroups', 'Reset'), 'javacript:void(0)', ['class' => 'btn btn-secondary reset-search']) ?>
            <?= Html::submitButton(Yii::t('amosgroups', 'Search'), ['class' => 'btn btn-navigation-primary search-button']) ?>
        </div>
    </div>

    <div class="clearfix"></div>
</div>



