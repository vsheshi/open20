<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni\views\discussioni-topic-wizard
 * @category   CategoryName
 */

use lispa\amos\core\forms\WizardPrevAndContinueButtonWidget;
use lispa\amos\discussioni\AmosDiscussioni;

/**
 * @var yii\web\View $this
 * @var lispa\amos\discussioni\models\DiscussioniTopic $model
 * @var string $finishMessage
 */

$this->title = $model;

?>

<div class="row m-b-30">
    <div class="col-xs-12">
        <div class="pull-left">
            <div class="like-widget-img color-primary ">
                <?= \lispa\amos\core\icons\AmosIcons::show('comment', [], 'dash'); ?>
            </div>
        </div>
        <div class="text-wrapper">
            <h3><?= $finishMessage ?></h3>
            <h4><?= AmosDiscussioni::tHtml('amosdiscussioni', "Click on 'back to discussions' to finish.") ?></h4>
        </div>
    </div>
</div>

<?= WizardPrevAndContinueButtonWidget::widget([
    'model' => $model,
    'previousUrl' => Yii::$app->getUrlManager()->createUrl(['/discussioni/discussioni-topic-wizard/summary', 'id' => $model->id]),
    'viewPreviousBtn' => false,
    'continueLabel' => AmosDiscussioni::tHtml('amosdiscussioni', 'Back to discussions'),
    'finishUrl' => Yii::$app->session->get(AmosDiscussioni::beginCreateNewSessionKey())
]) ?>
