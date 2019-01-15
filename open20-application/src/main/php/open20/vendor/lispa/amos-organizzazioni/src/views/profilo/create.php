<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

use lispa\amos\core\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var lispa\amos\organizzazioni\models\Profilo $model
 */
$this->title = 'Crea';
$this->params['breadcrumbs'][] = ['label' => 'Organizzazioni', 'url' => ['/organizzazioni']];
$this->params['breadcrumbs'][] = ['label' => 'Organizzazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="are-profilo-create">
    <?php echo $this->render('_form', [
		'model' => $model,
		'fid' => NULL,
		'dataField' => NULL,
		'dataEntity' => NULL,
	]) ?>

</div>
