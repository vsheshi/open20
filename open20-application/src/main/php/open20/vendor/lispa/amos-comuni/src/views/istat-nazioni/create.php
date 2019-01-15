<?php

use lispa\amos\core\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lispa\amos\comuni\models\IstatNazioni $model
 */

$this->title = 'Create Istat Nazioni';
$this->params['breadcrumbs'][] = ['label' => 'Istat Nazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="istat-nazioni-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
