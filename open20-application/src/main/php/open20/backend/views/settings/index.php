<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use yii\bootstrap\Button;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\admin\models\Settings;

/**
 * @var yii\web\View $this
 */

$this->title = $model;
$this->params['breadcrumbs'][] = AmosAdmin::t('amosplatform', 'Platform Configurator');

$components = \lispa\amos\admin\models\Settings::findAll(['route' => '/components/']);

function getActions($model)
{
    $html = Html::a(AmosIcons::show('pencil') . ' ' . AmosAdmin::t('amosplatform', 'Edit'),
        Yii::$app->urlManager->createUrl([
            '/settings/update',
            'id' => $model->id
        ]),
        [
            'class' => 'btn btn-primary',
        ]
    );

    if ($model->enabled) {
        $html .= Html::a(AmosIcons::show('pencil') . ' ' . AmosAdmin::t('amosplatform', 'Disable'),
            Yii::$app->urlManager->createUrl([
                '/settings/disable',
                'id' => $model->id
            ]),
            [
                'class' => 'btn btn-warning',
            ]
        );
    } else {
        $html .= Html::a(AmosIcons::show('pencil') . ' ' . AmosAdmin::t('amosmyactivities', 'Enable'),
            Yii::$app->urlManager->createUrl([
                '/settings/enable',
                'id' => $model->id
            ]),
            [
                'class' => 'btn btn-info',
            ]
        );
    }

    return $html;
}

function getDescription($model) {

    $className = Settings::findOne([
        'name' => 'class',
        'route' => $model->route . $model->name . '/'
    ]);

    if(!empty($className)) {
        $reflector = new ReflectionClass($className->value);
        $directory = dirname($reflector->getFileName());

        //Probably location is inside src
        $composerJson = $directory . '/../composer.json';

        if(file_exists($composerJson)) {
            $content = file_get_contents($composerJson);
            $jsonData = json_decode($content);

            if(property_exists($jsonData, 'description')) {
                return $jsonData->description;
            }
        }
    }

    return $model->descriptor;
}

$columns = [
    'name' => [
        //'attribute' => 'actions',
        'format' => 'text',
        'label' => AmosAdmin::t('amosplatform', 'Name'),
        'value' => function ($model) {
            return ucfirst($model->name);
        }
    ],
    'description' => [
        //'attribute' => 'actions',
        'format' => 'text',
        'label' => AmosAdmin::t('amosplatform', 'Description'),
        'value' => function ($model) {
            return getDescription($model);
        }
    ],
    'updated_at',
    'actions' => [
        //'attribute' => 'actions',
        'format' => 'html',
        'label' => AmosAdmin::t('amosplatform', 'Actions'),
        'value' => function ($model) {
            return getActions($model);
        }
    ],
];

?>

<div class="details_card">
    <div class="profile row nom">
        <div class="col-xs-12">
            <b><?= AmosAdmin::t('amosplatform', 'Settings Actions'); ?></b>

            <hr>
            <?php
            echo \lispa\amos\core\helpers\Html::a(\lispa\amos\core\icons\AmosIcons::show('refresh') . ' ' . AmosAdmin::t('amosplatform', 'Load From File'),
                Yii::$app->urlManager->createUrl([
                    '/settings/load-from-file'
                ]),
                [
                    'class' => 'btn btn-warning pull-left',
                    'data' => [
                        'confirm' => AmosAdmin::t('amosplatform', 'This action ERASE current settings')
                    ]
                ]
            );
            echo '&nbsp;';
            echo \lispa\amos\core\helpers\Html::a(\lispa\amos\core\icons\AmosIcons::show('check') . ' ' . AmosAdmin::t('amosplatform', 'Test Configurations'),
                Yii::$app->urlManager->createUrl([
                    '/settings/config-test'
                ]),
                [
                    'class' => 'btn btn-primary pull-right'
                ]
            );
            ?>
        </div>
    </div>
</div>

<hr>

<h3><?= AmosAdmin::t('amosplatform', 'Modules'); ?></h3>

<?php
$query = Settings::find();
$query->andWhere(['route' => '/modules/']);

echo \kartik\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $query
    ]),
    'columns' => $columns,
    'responsive' => true,
    'hover' => true
]);
?>

<h3><?= AmosAdmin::t('amosplatform', 'Components'); ?></h3>


<?php
$query = Settings::find();
$query->andWhere(['route' => '/components/']);

echo \kartik\grid\GridView::widget([
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $query
    ]),
    'columns' => $columns,
    'responsive' => true,
    'hover' => true
]);
?>

<hr>