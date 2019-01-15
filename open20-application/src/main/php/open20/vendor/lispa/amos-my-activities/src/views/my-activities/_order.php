<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\views\my-acivities
 * @category   CategoryName
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use lispa\amos\myactivities\AmosMyActivities;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\myactivities\models\search\MyActivitiesModelSearch $modelSort
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="news-order element-to-toggle" data-toggle-element="form-order">
    <div class="col-xs-12">
        <h2><?= AmosMyActivities::t('amosmyactivities', 'Sort by') ?>:</h2>
    </div>

    <?php $form = ActiveForm::begin([
        'action' => Yii::$app->controller->action->id,
        'method' => 'get',
        'options' => [
            'class' => 'default-form'
        ]
    ]);
    ?>

    <div class="col-sm-6 col-lg-4">
        <?= $form->field($modelSort, 'orderType')->dropDownList(
            [
                SORT_DESC => AmosMyActivities::t('amosmyactivities', 'Descending'),
                SORT_ASC => AmosMyActivities::t('amosmyactivities', 'Ascending'),
            ]
        )
        ?>
    </div>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::a(AmosMyActivities::t('amosmyactivities', 'Reset'), [Yii::$app->controller->action->id], ['class'=>'btn btn-secondary']) ?>
            <?= Html::submitButton(AmosMyActivities::t('amosmyactivities', 'Sort'), ['class' => 'btn btn-navigation-primary']) ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>

</div>