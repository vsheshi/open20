<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

use lispa\amos\core\views\AmosGridView;
use lispa\amos\discussioni\AmosDiscussioni;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\discussioni\models\search\DiscussioniCommentiSearch $searchModel
 */

$this->title = AmosDiscussioni::t('amosdiscussioni', 'Discussioni Commenti');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discussioni-commenti-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin();
    echo AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'testo:ntext',
            'discussioni_risposte_id',
            ['attribute' => 'created_at', 'format' => ['datetime', (isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
            ['attribute' => 'updated_at', 'format' => ['datetime', (isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
//            ['attribute'=>'deleted_at','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']], 
//            'created_by', 
//            'updated_by', 
//            'deleted_by', 
//            'version', 

            [
                'class' => 'lispa\amos\core\views\grid\ActionColumn',
            ],
        ],
    ]);
    Pjax::end(); ?>

</div>
