<?php
$this->title = 'Import list';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php 
    echo \lispa\amos\core\views\AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name_file',
            [
                'attribute' => 'successfull',
                'format' => 'boolean'
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime'
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{download}',
                'buttons' => [
                    'download' => function($url, $model){
                        return \yii\helpers\Html::a(\lispa\amos\core\icons\AmosIcons::show('download'),['/import/default/generate-excel', 'id' => $model->id], [
                            'class' => 'btn btn-tools-secondary'
                        ]);
                    }
                ]
            ]
        ]
    ]);
?>
