<?php

use lispa\amos\core\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lispa\amos\comuni\models\IstatProvince $model
 */

$this->title = 'Crea';
$this->params['breadcrumbs'][] = ['label' => 'Comuni', 'url' => ['/comuni/dashboard/index']];
$this->params['breadcrumbs'][] = ['label' => 'Elenco province', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="istat-province-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
