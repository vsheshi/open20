<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */

use lispa\amos\emailmanager\AmosEmail;
use lispa\amos\core\views\DataProviderView;
use lispa\amos\core\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EmailSpoolSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = AmosEmail::t('amosemail','Email Spools');
$this->params['breadcrumbs'][] = ['label' => AmosEmail::t('amosemail', 'Email Manager'), 'url' => ['/email']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-spool-index">
    <?php echo $this->render('_search', ['model' => $model]); ?>
    <?php echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                //'transport',
                //'template',
                //'priority',
                'status',
                // 'model_name',
                // 'model_id',
                [
                    'attribute' => 'to_address',
                    'headerOptions' => ['style' => 'width:100px'],
                ],
                [
                    'attribute' => 'from_address',
                    'headerOptions' => ['style' => 'width:100px'],
                ],
                'subject',
                /*[
                    'attribute' => 'message',
                    'format' => 'ntext',
                    'headerOptions' => ['style' => 'width:300px'],
                ],*/
                // 'bcc:ntext',
                // 'files:ntext',
                // 'sent',
                [
                    'attribute' => 'created_at',
                    'format' => ['date', 'php:d/m/Y H:i:s'],
                ],
                // 'updated_at',

                ['class' => 'lispa\amos\core\views\grid\ActionColumn',
                    'headerOptions' => ['style' => 'width:100px'],
                ],
            ],
        ],
        
    ]); ?>
</div>
