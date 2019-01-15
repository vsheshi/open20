<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\views\my-activities
 * @category   CategoryName
 */

/**
 * @var \lispa\amos\myactivities\models\search\MyActivitiesModelSearch $modelSearch
 */

?>

<?php

use lispa\amos\myactivities\AmosMyActivities;
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\helpers\Html;

?>

<div class="myactivities-search element-to-toggle" data-toggle-element="form-search">
    <div class="col-xs-12"><h2><?= AmosMyActivities::t('amosmyactivities', 'Search for') ?>:</h2></div>

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="col-xs-12">
        <?= $form->field($modelSearch, 'searchString') ?>
    </div>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::submitButton(AmosMyActivities::t('amosmyactivities', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(AmosMyActivities::t('amosmyactivities', 'Reset'),[Yii::$app->controller->action->id], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="clearfix"></div>

</div>