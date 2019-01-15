<?php

use lispa\amos\core\helpers\Html;
use lispa\amos\core\views\DataProviderView;
use yii\widgets\Pjax;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\groups\Module;
use yii\bootstrap\Modal;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \lispa\amos\groups\models\search\GroupsSearch $model
 */
$this->title = Yii::t('amosgroups', 'Gruppi');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="groups-index">
    <?php // echo $this->render('_search', ['model' => $model]);  ?>

    <p>
        <?php /* echo         Html::a(Yii::t('cruds', 'Nuovo {modelClass}', [
          'modelClass' => 'Groups',
          ])        , ['create'], ['class' => 'btn btn-amministration-primary']) */ ?>
    </p>
    <?php
    $isVisible                     = true;
    $cwh                           = Yii::$app->getModule("cwh");
    $community                     = Yii::$app->getModule("community");
    if (isset($cwh) && isset($community)) {
        $cwh->setCwhScopeFromSession();
        if (!empty($cwh->userEntityRelationTable)) {
            $entityId = $cwh->userEntityRelationTable['entity_id'];
            if (!empty($entityId)) {
                $isVisible = false;
            }
        }
    }
    ?>
    <?php
    echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
//                'id',
//                'parent_id',
                'name',
                'description',
                [
                    'attribute' => 'parent_id',
                    'visible' => $isVisible
                ],
                ['attribute' => 'created_at', 'format' => ['datetime', (isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime']))
                                ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
//            ['attribute'=>'updated_at','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']], 
//            ['attribute'=>'deleted_at','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']], 
//            'created_by', 
//            'updated_by', 
//            'deleted_by', 
                [
                    'class' => 'lispa\amos\core\views\grid\ActionColumn',
                    'template' => '{send}{view}{update}{delete}',
                    'buttons' => [
                        'send' => function ($url, $model) {
                            /** @var \lispa\amos\admin\models\UserProfile $model */
                            $utente    = Yii::$app->getUser();
                            /** @var \lispa\amos\cwh\AmosCwh $moduleCwh */
                            $moduleCwh = Yii::$app->getModule('cwh');
                            $community = new \lispa\amos\community\models\Community;
                            $scope = null;
                            if (!empty($moduleCwh)) {
                                $scope = $moduleCwh->getCwhScope();
                            }
                            if (!empty($scope)) {
                                if (isset($scope['community'])) {
                                    $communityId = $scope['community'];
                                    $community = \lispa\amos\community\models\Community::findOne($communityId);
                                }
                            }
                            if ($community->isCommunityManager() || $utente->can('ADMIN')) {
                                return Html::a(AmosIcons::show('email'),
                                        Yii::$app->urlManager->createUrl(['/groups/groups/send-email-to-group', 'idGroup' => $model->id]),
                                        [
                                           'class' => 'btn btn-tool-secondary',
                                           'title' => Module::t('amosadmin', 'Invia una notifica al gruppo')
                                ]);
                            } else {
                                return '';
                            }
                        }
                    ]
                ],
            ],
        ],
        /* 'listView' => [
          'itemView' => '_item'
          'masonry' => FALSE,

          // Se masonry settato a TRUE decommentare e settare i parametri seguenti
          // nel CSS settare i seguenti parametri necessari al funzionamento tipo
          // .grid-sizer, .grid-item {width: 50&;}
          // Per i dettagli recarsi sul sito http://masonry.desandro.com

          //'masonrySelector' => '.grid',
          //'masonryOptions' => [
          //    'itemSelector' => '.grid-item',
          //    'columnWidth' => '.grid-sizer',
          //    'percentPosition' => 'true',
          //    'gutter' => '20'
          //]
          ],
          'iconView' => [
          'itemView' => '_icon'
          ],
          'mapView' => [
          'itemView' => '_map',
          'markerConfig' => [
          'lat' => 'domicilio_lat',
          'lng' => 'domicilio_lon',
          'icon' => 'iconaMarker',
          ]
          ],
          'calendarView' => [
          'itemView' => '_calendar',
          'clientOptions' => [
          //'lang'=> 'de'
          ],
          'eventConfig' => [
          //'title' => 'titoloEvento',
          //'start' => 'data_inizio',
          //'end' => 'data_fine',
          //'color' => 'coloreEvento',
          //'url' => 'urlEvento'
          ],
          'array' => false,//se ci sono più eventi legati al singolo record
          //'getEventi' => 'getEvents'//funzione da abilitare e implementare nel model per creare un array di eventi legati al record
          ] */
    ]);
    ?>

</div>