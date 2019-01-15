<?php

use lispa\amos\core\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lispa\amos\comuni\models\IstatRegioni $model
 */

$this->title = 'Create Istat Regioni';
$this->params['breadcrumbs'][] = ['label' => 'Istat Regioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="istat-regioni-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
