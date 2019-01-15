<?php

use lispa\amos\core\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lispa\amos\comuni\models\IstatContinenti $model
 */

$this->title = 'Create Istat Continenti';
$this->params['breadcrumbs'][] = ['label' => 'Istat Continenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="istat-continenti-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
