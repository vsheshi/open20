<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */

use lispa\amos\core\helpers\Html;


/* @var $this yii\web\View */
/* @var $model lispa\amos\emailmanager\models\EmailSpool */

$this->title = 'Create Email Spool';
$this->params['breadcrumbs'][] = ['label' => 'Email Spools', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-spool-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
