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
use lispa\amos\admin\base\ConfigurationManager;

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

?>

<section class="section-data">
    <?php if ($adminModule->confManager->isVisibleField('privacy', ConfigurationManager::VIEW_TYPE_FORM)): ?>
        <div class="row">
            <div class="col-xs-3">
                <?= $form->field($model, 'privacy')->checkbox() ?>
            </div>
            <div class="col-xs-4 m-t-20">
                <a href='/site/privacy' target='_blank'>  <?=AmosAdmin::t('amosadmin','Visualizza il documento della privacy')?> </a>
            </div>
        </div>
    <?php endif; ?>
</section>
