<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

use yii\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var lispa\amos\organizzazioni\models\Profilo $model
 */
$this->title = 'Aggiorna';
$this->params['breadcrumbs'][] = ['label' => 'Organizzazioni', 'url' => ['/organizzazioni']];
$this->params['breadcrumbs'][] = ['label' => 'Organizzazioni', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => strip_tags($model), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="are-profilo-update">

    <?php echo $this->render('_form', [
		'model' => $model,
		'fid' => NULL,
		'dataField' => NULL,
		'dataEntity' => NULL,
	]) ?>

</div>
