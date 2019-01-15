<?php

use yii\helpers\Html;
use lispa\amos\comuni\AmosComuni;

/**
 * @var yii\web\View $this
 * @var lispa\amos\comuni\models\IstatProvince $model
 */

$this->title = AmosComuni::t('amoscomuni', $model->nome);
$this->params['breadcrumbs'][] = ['label' => 'Comuni', 'url' => ['/comuni/dashboard/index']];
$this->params['breadcrumbs'][] = ['label' => 'Elenco province', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="istat-province-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
