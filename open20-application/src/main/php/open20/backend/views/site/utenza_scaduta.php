<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
$this->title = Yii::t('amosplatform', 'Utenza non attiva');
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- logo -->
<div id="bk-formDefaultLogin" class="bk-loginContainer">
    <h2><?= Html::encode($this->title) ?></h2>
    <hr class="bk-hrLogin">
    <p><?= Yii::t('amosplatform', $message); ?></p>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="form-group">
            </div>
            <div class="clear"></div>
            <hr class="bk-hrLogin">
        </div>
    </div>
</div>