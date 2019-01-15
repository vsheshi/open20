<?php

use lispa\amos\core\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lispa\amos\comuni\models\IstatCittaMetropolitane $model
 */

$this->title = 'Create Istat Citta Metropolitane';
$this->params['breadcrumbs'][] = ['label' => 'Istat Citta Metropolitane', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="istat-citta-metropolitane-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
