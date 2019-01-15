<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\views\my-activities
 * @category   CategoryName
 */

use lispa\amos\core\views\DataProviderView;
use lispa\amos\myactivities\AmosMyActivities;
use lispa\amos\myactivities\assets\ModuleMyActivitiesAsset;

ModuleMyActivitiesAsset::register($this);

/**
 * @var yii\web\View $this
 * @var yii\web\View $currentView
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \lispa\amos\myactivities\models\search\MyActivitiesModelSearch $model
 */

$this->title = ""; 
$this->params['breadcrumbs'][] = ['label' => AmosMyActivities::t('amosmyactivities', 'My activities'), 'url' => ['/my-activities']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="my-activities-index">
    <?php
    echo $this->render('_search', ['modelSearch' => $model]);
    echo $this->render('_order', ['modelSort' => $model]);
    ?>

    <?php
    if (!$parametro['empty']) {
        echo DataProviderView::widget(
            [
                'dataProvider' => $dataProvider,
                'currentView' => $currentView,
                'listView' => [
                    'itemView' => '_switch_item',
                ],
            ]);
    }
    ?>

</div>
