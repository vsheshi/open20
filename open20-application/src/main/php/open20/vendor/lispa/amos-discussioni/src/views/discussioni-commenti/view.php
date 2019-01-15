<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

use lispa\amos\discussioni\AmosDiscussioni;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var lispa\amos\discussioni\models\DiscussioniCommenti $model
 */
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => AmosDiscussioni::t('amosdiscussioni', 'Discussioni Commenti'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discussioni-commenti-view">

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'testo:ntext',
            'discussioni_risposte_id',
            [
                'attribute' => 'created_at',
                'format' => ['datetime', (isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['datetime', (isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
            ],
            [
                'attribute' => 'deleted_at',
                'format' => ['datetime', (isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
            ],
            'created_by',
            'updated_by',
            'deleted_by',
            'version',
        ]
    ])
    ?>

</div>
