<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos-report
 * @category   CategoryName
 */

use lispa\amos\core\views\DataProviderView;
use lispa\amos\report\AmosReport;
use yii\widgets\Pjax;
use lispa\amos\core\helpers\Html;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\report\models\search\ReportSearch $searchModel
 * @var \lispa\amos\dashboard\models\AmosUserDashboards $currentDashboard
 *
 */

?>
<div class="report-index">
    <?php
    echo $this->render('_search', ['model' => $model]);
    echo $this->render('_order', ['model' => $model]);
    ?>
<?php
Pjax::begin();
echo DataProviderView::widget([
    'dataProvider' => $dataProvider,
    'currentView' => $currentView,
    'gridView' => [
        //'filterModel' => $model,
        'columns' => [
            'content' => [
                'value' => function($model) {
                    return StringHelper::truncateWords($model->content,20,'...');
                }
            ],
            'created_by' => [
                'attribute' => 'createdUserProfile',
                'label' => AmosReport::t('amosreport', 'Pubblicato Da')
            ],
            'created_at' => [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return (is_null($model->created_at)) ? 'Subito' : Yii::$app->formatter->asDate($model->data_pubblicazione);
                }
            ],
            'status' => [
                'attribute' => 'status',
            ],
            [
                'class' => 'lispa\amos\core\views\grid\ActionColumn',
            ]
        ]
    ]
]);
Pjax::end();
?>

</div>