<?php

use lispa\amos\core\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lispa\amos\comuni\models\IstatUnioneDeiComuni $model
 */

$this->title = 'Create Istat Unione Dei Comuni';
$this->params['breadcrumbs'][] = ['label' => 'Istat Unione Dei Comuni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="istat-unione-dei-comuni-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
