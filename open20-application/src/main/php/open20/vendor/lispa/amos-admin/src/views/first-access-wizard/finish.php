<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\first-access-wizard
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\core\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \lispa\amos\admin\models\UserProfile $model
 */

?>

<div class="col-xs-12">
    <div class="row">
        <div class="col-xs-12">
            <h4><?= AmosAdmin::tHtml('amosadmin', "#faw_finish_text_1",[
                    'name' => $model->nome,
                    'lastname' => $model->cognome,
                ]) ?></h4>
            <h4><?= AmosAdmin::tHtml('amosadmin', "#faw_finish_text_2") ?></h4>
            <h4><?= AmosAdmin::tHtml('amosadmin', "#faw_finish_text_3",[
                    'textBtn' => AmosAdmin::tHtml('amosadmin', 'Enter'),
                    'appName' => Yii::$app->name,
                ]) ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <?= Html::a(AmosAdmin::tHtml('amosadmin', 'Enter'), ['/dashboard'], ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>
</div>
