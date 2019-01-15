<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\views\documenti
 * @category   CategoryName
 */

use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\utilities\ModalUtility;
use lispa\amos\core\views\DataProviderView;
use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\documenti\models\Documenti;
use lispa\amos\documenti\utility\DocumentsUtility;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\documenti\models\search\DocumentiSearch $model
 * @var \lispa\amos\dashboard\models\AmosUserDashboards $currentDashboard
 */

/** @var \lispa\amos\documenti\controllers\DocumentiController $controller */
$controller = Yii::$app->controller;

$js = <<<JS
    $('.documenti-search button[type="submit"]').click(function(){
        $('.loading').show();
    })
JS;
$this->registerJs($js);
\lispa\amos\layout\assets\SpinnerWaitAsset::register($this);

$actionColumnDefault = '{view}{update}{delete}';
$actionColumnToValidate = '{validate}{reject}';
$actionColumn = $actionColumnDefault;
$actionId = $controller->action->id;
if ($actionId == 'to-validate-documents') {
    $actionColumn = $actionColumnToValidate . $actionColumnDefault;
}
$enableVersioning = $controller->documentsModule->enableDocumentVersioning;
if ($enableVersioning) {
    $actionColumn =  '{view}{newDocVersion}{update}{delete}';
}

$foldersEnabled = $controller->documentsModule->enableFolders;
$enableCategories = $controller->documentsModule->enableCategories;
$hidePubblicationDate = $controller->documentsModule->hidePubblicationDate;
$showCountDocumentRecursive = $controller->documentsModule->showCountDocumentRecursive;


$columns = [];
if ($foldersEnabled) {
    $columns['type'] = [
        'label' => AmosDocumenti::t('amosdocumenti', '#type'),
        'format' => 'html',
        'value' => function ($model) {
        $title = AmosDocumenti::t('amosdocumenti', 'Documenti');
            if($model->is_folder) {
                $title  = AmosDocumenti::t('amosdocumenti', '#folder');
            }
            else {
                $documentFile = $model->getDocumentMainFile();
                if($documentFile) {
                    $title = $documentFile->type;
                }
            }

            $icon = DocumentsUtility::getDocumentIcon($model, true);
            return AmosIcons::show($icon, ['title' => $title], 'dash');
        }
    ];
    $columns['titolo'] = [
        'attribute' => 'titolo',
        'headerOptions' => [
            'id' => $model->getAttributeLabel('titolo')
        ],
        'contentOptions' => [
            'headers' => $model->getAttributeLabel('titolo')
        ],
        'format' => 'html',
        'value' => function ($model) use ($actionId) {
            /** @var Documenti $model */
            $title = $model->titolo;
            if ($model->is_folder) {
                $url = [$actionId, 'parentId' => $model->id];
            } else {
                $url = '';
                $document  = $model->getDocumentMainFile();
                if($document) {
                    $url = $document->getUrl();
                }
            }

            if($model->is_folder) {
                $mouseover = 'Accedi alla cartella';
            }
            else {
                $mouseover = AmosDocumenti::t('amosdocumenti', 'Scarica il documento');
            }
            return Html::a($title, $url, ['title' => $mouseover .' "' . $model->titolo . '"']);
        }
    ];
	$columns['created_by'] = [
        'attribute' => 'createdUserProfile',
        'label' => AmosDocumenti::t('amosdocumenti', 'Creato da'),
        'value' => function($model){
            $profile = \lispa\amos\admin\models\UserProfile::find()->andWhere(['user_id' => $model->created_by])->one();
            return Html::a($profile->nomeCognome, ['/admin/user-profile/view', 'id' => $profile->id ], [
                'title' => AmosDocumenti::t('amosdocumenti', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $profile->nomeCognome])
            ]);
        },
        'format' => 'html'
    ];
    $columns['updated_by'] = [
        'attribute' => 'updatedUserProfile',
        'label' => AmosDocumenti::t('amosdocumenti', 'Aggiornato da'),
        'value' => function($model){
            $profile = \lispa\amos\admin\models\UserProfile::find()->andWhere(['user_id' => $model->updated_by])->one();
            return Html::a($profile->nomeCognome, ['/admin/user-profile/view', 'id' => $profile->id ], [
                'title' => AmosDocumenti::t('amosdocumenti', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $profile->nomeCognome])
            ]);
        },
        'format' => 'html'
    ];
    $columns[] = [
        'label' => AmosDocumenti::t('amosdocumenti', 'Documents'),
        'value' => function($model) use ($showCountDocumentRecursive) {
            if($model->is_folder) {
                if($showCountDocumentRecursive) {
                    return count($model->allDocumentChildrens);
                }
                else {
                    return count($model->documentChildrens);
                }
            }else {
                return '';
            }
        },
    ];
} else {
    $columns['titolo'] = [
        'attribute' => 'titolo',
            'headerOptions' => [
            'id' => $model->getAttributeLabel('titolo')
        ]
    ];
    $columns['created_by'] = [
        'attribute' => 'createdUserProfile',
        'label' => AmosDocumenti::t('amosdocumenti', 'Pubblicato Da'),
    ];
}
    $columns['updated_at'] = [
        'label' => AmosDocumenti::t('amosdocumenti', '#uploaded_at'),
        'attribute' => 'data_pubblicazione',
        'value' => function ($model) {
            /** @var Documenti $model */
            return (is_null($model->updated_at)) ? 'Subito' : Yii::$app->formatter->asDate($model->updated_at);
        }
    ];

if (!$foldersEnabled) {
    $columns['data_rimozione'] = [
        'attribute' => 'data_rimozione',
        'value' => function ($model) {
            /** @var Documenti $model */
            return (is_null($model->data_rimozione)) ? 'Mai' : Yii::$app->formatter->asDate($model->data_rimozione);
        }
    ];

    $columns['status'] = [
        'attribute' => 'status',
        'value' => function ($model) {
            /** @var Documenti $model */
            return $model->hasWorkflowStatus() ? $model->getWorkflowStatus()->getLabel() : '--';
        }
    ];
}

if($enableCategories){
    $columns['documenti_categorie_id'] = [
        'attribute' => 'documentiCategorie.titolo',
        'label' => AmosDocumenti::t('amosdocumenti', 'Categoria'),
    ];
}

if ($controller->documentsModule->enableDocumentVersioning) {
    $columns['version'] = [
        'attribute' => 'version',
        'value' => function ($model) {
            /** @var Documenti $model */
            return (!$model->is_folder && $model->version ? $model->version : '');
        },
    ];
}

//the columns for export have to be before the special columns (ExpandRowColumn, Action column)
$exportColumns = $columns;
$exportColumns['type'] =  [
        'value' => function ($model) {
            if ($model->is_folder) {
                $return = AmosDocumenti::t('amosdocumenti', '#folder');
            } else {
                $return = AmosDocumenti::t('amosdocumenti', '#document');
            }
            return $return;
        }
];



if($controller->documentsModule->enableDocumentVersioning && !$model->is_folder) {
    $columns['expandAllTitle'] = [
        'class' => 'kartik\grid\ExpandRowColumn',
        'expandAllTitle' =>  AmosDocumenti::t('amosdocumenti','Version'),
        'allowBatchToggle' => false,
        'header' => AmosDocumenti::t('amosdocumenti', 'Expand / Collapse'),
        'headerOptions' => [
            'style' => 'white-space: nowrap;',
        ],
        'contentOptions' => [
            'class' => 'text-center',
        ],
        'value' => function ($model, $key, $index, $column) {
            $queryParams = \Yii::$app->request->getQueryParams();
            $queryParams['parent_id'] = $model->id;

            /** @var  $dataProvider \yii\data\ActiveDataProvider*/
            $modelSearch = new \lispa\amos\documenti\models\search\DocumentiSearch();
            $dataProvider = $modelSearch->searchVersions($queryParams);

            if(!$model->is_folder && $dataProvider->count > 0) {
                return \kartik\grid\GridView::ROW_COLLAPSED;
            }
            else return '';
        },
        'expandIcon' => AmosIcons::show('caret-down', ['title' => AmosDocumenti::t('amosdocumenti','#expand_title')]),
        'collapseIcon' => AmosIcons::show('caret-up',  ['title' => AmosDocumenti::t('amosdocumenti','#collapse_title')]),
        'expandTitle' => AmosDocumenti::t('amosdocumenti', ''),
        'collapseTitle' => AmosDocumenti::t('amosdocumenti', ''),
        'detailUrl' => \yii\helpers\Url::to(['/documenti/documenti/list-only'])
    ];
}

$columns[] = [
    'class' => 'lispa\amos\core\views\grid\ActionColumn',
    'template' => $actionColumn,
    'buttons' => [
        'view' => function ($url, $model) {
            /** @var Documenti $model */
            $btn = '';
            if (Yii::$app->getUser()->can('DOCUMENTI_READ', ['model' => $model])) {
                $btn = Html::a(AmosIcons::show('file'), ['view', 'id' => $model->id], [
                    'class' => 'btn btn-tools-secondary',
                    'title' => AmosDocumenti::t('amosdocumenti', 'Open the card')
                ]);
            }
            return $btn;
        },
        'newDocVersion' => function ($url, $model) {
            /** @var Documenti $model */
            /** @var \lispa\amos\documenti\controllers\DocumentiController $controller */
            $controller = Yii::$app->controller;
            $btn = '';
            if (Yii::$app->getUser()->can('DOCUMENTI_UPDATE', ['model' => $model])) {
                if ($controller->documentsModule->enableDocumentVersioning && !$model->is_folder && (is_null($model->version_parent_id))) {
                    $btn = ModalUtility::addConfirmRejectWithModal([
                        'modalId' => 'new-document-version-modal-id-' . $model->id,
                        'modalDescriptionText' => AmosDocumenti::t('amosdocumenti', '#NEW_DOCUMENT_VERSION_MODAL_TEXT'),
                        'btnText' => AmosIcons::show('edit', ['class' => '']),
                        'btnLink' => Yii::$app->urlManager->createUrl([
                            '/documenti/documenti/new-document-version',
                            'id' => $model['id']
                        ]),
                        'btnOptions' => ['title' => AmosDocumenti::t('amosdocumenti', 'New document version'), 'class' => 'btn btn-tools-secondary']
                    ]);
                }
            }
            return $btn;
        },
        'validate' => function ($url, $model) {
            /** @var Documenti $model */
            $btn = '';
            if (Yii::$app->getUser()->can('DocumentValidate', ['model' => $model])) {
                $btn = ModalUtility::addConfirmRejectWithModal([
                    'modalId' => 'validate-document-modal-id',
                    'modalDescriptionText' => AmosDocumenti::t('amosdocumenti', '#VALIDATE_DOCUMENT_MODAL_TEXT'),
                    'btnText' => AmosIcons::show('check-circle', ['class' => '']),
                    'btnLink' => Yii::$app->urlManager->createUrl([
                        '/documenti/documenti/validate-document',
                        'id' => $model['id']
                    ]),
                    'btnOptions' => ['title' => AmosDocumenti::t('amosdocumenti', 'Publish'), 'class' => 'btn btn-tools-secondary']
                ]);
            }
            return $btn;
        },
        'reject' => function ($url, $model) {
            /** @var Documenti $model */
            $btn = '';
            if (Yii::$app->getUser()->can('DocumentValidate', ['model' => $model])) {
                $btn = ModalUtility::addConfirmRejectWithModal([
                    'modalId' => 'reject-document-modal-id',
                    'modalDescriptionText' => AmosDocumenti::t('amosdocumenti', '#REJECT_DOCUMENT_MODAL_TEXT'),
                    'btnText' => AmosIcons::show('minus-circle', ['class' => '']),
                    'btnLink' => Yii::$app->urlManager->createUrl([
                        '/documenti/documenti/reject-document',
                        'id' => $model['id']
                    ]),
                    'btnOptions' => ['title' => AmosDocumenti::t('amosdocumenti', 'Reject'), 'class' => 'btn btn-tools-secondary']
                ]);
            }
            return $btn;
        },
        'update' => function ($url, $model) use ($enableVersioning) {
            /** @var Documenti $model */
            $btn = '';
            if(($enableVersioning && $model->is_folder) || !$enableVersioning ){
                if (Yii::$app->user->can('DOCUMENTI_UPDATE', ['model' => $model]) ) {
                    $action = '/documenti/documenti/update?id=' . $model->id;
                    $options = ModalUtility::getBackToEditPopup($model, 'DocumentValidate', $action, [
                        'class' => 'btn btn-tools-secondary',
                        'title' => Yii::t('amoscore', 'Modifica'),
                        'data-pjax' => '0'
                    ]);
                    return Html::a(\lispa\amos\core\icons\AmosIcons::show('edit'), $action, $options);
                }
            }
            return $btn;
        }
    ]
];

?>
<div class="loading" hidden></div>
<div class="documents-index">
    <?php
    $parentId = '';
    if(!empty(\Yii::$app->request->get('parentId'))){
        $parentId = "?parentId=".\Yii::$app->request->get('parentId');
    }
    echo $this->render('_search', [
        'model' => $model,
        'originAction' => Yii::$app->controller->action->id.$parentId
    ]);
    echo $this->render('_order', [
        'model' => $model,
        'originAction' => Yii::$app->controller->action->id
    ]);


    echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        'currentView' => $currentView,
        'gridView' => [
            'rowOptions'=>function($model){
                return ['class' => 'kv-disable-click'];
            },
            'columns' => $columns,
            'enableExport' => true
        ],
        'listView' => [
            'itemView' => '_item',
            /*'masonry' => TRUE,
            'masonrySelector' => '.grid',
            'masonryOptions' => [
                'itemSelector' => '.grid-item',
                'columnWidth' => '.grid-sizer',
                'percentPosition' => 'true',
                'gutter' => 20
            ],*/
        ],
        'exportConfig' => [
            'exportEnabled' => true,
            'exportColumns' => $exportColumns
        ]
    ]);
    ?>
</div>

