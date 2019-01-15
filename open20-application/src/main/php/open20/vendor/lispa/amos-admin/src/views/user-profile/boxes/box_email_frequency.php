<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile\boxes
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\notificationmanager\widgets\NotifyFrequencyWidget;

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

?>

<section class="m-t-30">
    <h2>
        <?= AmosIcons::show('email') ?>
        <?= AmosAdmin::tHtml('amosadmin', 'Email frequency') ?>
    </h2>
    <p><?= AmosAdmin::t('amosadmin', 'If the frequency is not indicated, you will receive the notifications as automatically set by the system') . '.' ?></p>
    <div class="col-xs-12 nop">
        <?= NotifyFrequencyWidget::widget([
            'model' => $model
        ]) ?>
    </div>
    <div class="clearfix"></div>
</section>
