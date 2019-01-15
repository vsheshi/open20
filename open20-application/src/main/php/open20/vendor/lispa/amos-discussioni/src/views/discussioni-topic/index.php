<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\utilities\ModalUtility;
use lispa\amos\core\views\DataProviderView;
use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\discussioni\models\DiscussioniTopic;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lispa\amos\discussioni\models\search\DiscussioniTopicSearch $model
 * @var string $currentView
 */

//$this->title = AmosDiscussioni::t('amosdiscussioni', 'Discussioni');

$actionColumnDefault = '{partecipa} {update} {delete}';
$actionColumnToValidate = '{validate}{reject}';
$actionColumn = $actionColumnDefault;
if (Yii::$app->controller->action->id == 'to-validate-discussions') {
    $actionColumn = $actionColumnToValidate . $actionColumnDefault;
}

?>

<div class="discussions-topic-index">
    <?= $this->render('_search', [
        'model' => $model,
        'originAction' => Yii::$app->controller->action->id
    ]); ?>
    <?= $this->render('_order', [
        'model' => $model,
        'originAction' => Yii::$app->controller->action->id
    ]); ?>
    <?= DataProviderView::widget(
        [
            'dataProvider' => $dataProvider,
            'currentView' => $currentView,
            'gridView' => [
                'columns' => [
                    'immagine' => [
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Immagine'),
                        'format' => 'html',
                        'value' => function ($model) {
                            /** @var DiscussioniTopic $model */
                            $url = '/img/img_default.jpg';
                            if (!is_null($model->discussionsTopicImage)) {
                                $url = $model->discussionsTopicImage->getUrl('square_small', false, true);
                            }
                            $contentImage = Html::img($url, ['class' => 'gridview-image', 'alt' => AmosDiscussioni::t('amosdiscussioni', 'Immagine della discussione')]);
                            return Html::a($contentImage, $model->getFullViewUrl());
                        }
                    ],
                    'titolo',
                    [
                        'attribute' => 'created_by',
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Pubblicato Da'),
                        'value' => function($model){
                            $pubblicatoDa = UserProfile::findOne(['user_id' => $model->created_by]);
                            return Html::a($pubblicatoDa->getNomeCognome(), ['/admin/user-profile/view', 'id' => $pubblicatoDa->id ], [
                                'title' => AmosDiscussioni::t('amosnews', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $pubblicatoDa->getNomeCognome()])
                            ]);
                        },
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Data pubblicazione'),
                        'format' => 'datetime',
                    ],
                    'status' => [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            /** @var DiscussioniTopic $model */
                            return $model->hasWorkflowStatus() ? $model->getWorkflowStatus()->getLabel() : '--';
                        }
                    ],
                    [
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Contributi'),
                        'value' => function ($model) {
                            /** @var DiscussioniTopic $model */
                            return $model->getDiscussionComments()->count();
                        }
                    ],
                    'hints',
                    [
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Partecipanti'),
                        'value' => function ($model) {
                            /** @var DiscussioniTopic $model */
                            $risposte = $model->getDiscussionComments();
                            return $risposte->groupBy('created_by')->count();
                        }
                    ],
                    [
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Ultimo Commento/Risposta'),
                        'format' => 'html',
                        'value' => function ($model) {
                            /** @var DiscussioniTopic $model */
                            if ($model->lastCommentUser) {
                                $ultima_risposta = $model->lastCommentUser->nome . ' ' . $model->lastCommentUser->cognome;
                                $data = '<br>' . Yii::$app->formatter->asDatetime($model->lastCommentDate);
                                return $ultima_risposta . $data;
                            } else {
                                return AmosDiscussioni::t('amosdiscussioni', 'Non ci sono ancora contributi');
                            }
                        }
                    ],
                    /*[
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Testo'),
                        'format' => 'html',
                        'value' => function ($data) {
                            if (strlen($data->testo) > 1000) {
                                $stringCut = substr($data->testo, 0, 1000);
                                return substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                            } else {
                                return $data->testo;
                            }
                        }
                    ],*/
                    
                    [
                        'class' => 'lispa\amos\core\views\grid\ActionColumn',
                        'template' => $actionColumn,
                        'buttons' => [
                            'validate' => function ($url, $model) {
                                /** @var DiscussioniTopic $model */
                                $btn = '';
                                if (Yii::$app->getUser()->can('DiscussionValidate', ['model' => $model])) {
                                    $btn = ModalUtility::addConfirmRejectWithModal([
                                        'modalId' => 'validate-discussion-topic-modal-id',
                                        'modalDescriptionText' => AmosDiscussioni::t('amosdiscussioni', '#VALIDATE_DISCUSSION_MODAL_TEXT'),
                                        'btnText' => AmosIcons::show('check-circle', ['class' => '']),
                                        'btnLink' => Yii::$app->urlManager->createUrl(['/discussioni/discussioni-topic/validate-discussion', 'id' => $model['id']]),
                                        'btnOptions' => ['title' => AmosDiscussioni::t('amosdiscussioni', 'Publish'),'class' => 'btn btn-tools-secondary']
                                    ]);
                                }
                                return $btn;
                            },
                            'reject' => function ($url, $model) {
                                /** @var DiscussioniTopic $model */
                                $btn = '';
                                if (Yii::$app->getUser()->can('DiscussionValidate', ['model' => $model])) {
                                    $btn = ModalUtility::addConfirmRejectWithModal([
                                        'modalId' => 'reject-discussion-topic-modal-id',
                                        'modalDescriptionText' => AmosDiscussioni::t('amosdiscussioni', '#REJECT_DISCUSSION_MODAL_TEXT'),
                                        'btnText' => AmosIcons::show('minus-circle', ['class' => '']),
                                        'btnLink' => Yii::$app->urlManager->createUrl(['/discussioni/discussioni-topic/reject-discussion', 'id' => $model['id']]),
                                        'btnOptions' => ['title' => AmosDiscussioni::t('amosdiscussioni', 'Reject'), 'class' => 'btn btn-tools-secondary']
                                    ]);
                                }
                                return $btn;
                            },
                            'partecipa' => function ($url, $model, $key) {
                                /** @var DiscussioniTopic $model */
                                return Html::a(AmosIcons::show('comment'), [$url], ['class' => 'btn btn-tool-secondary','title' => AmosDiscussioni::t('amosdiscussioni', 'Partecipa')]);
                            },
                            'update' => function ($url, $model) {
                                if (Yii::$app->user->can('DISCUSSIONITOPIC_UPDATE', ['model' => $model])) {
                                    $action = '/discussioni/discussioni-topic/update?id=' . $model->id;
                                    $options = \lispa\amos\core\utilities\ModalUtility::getBackToEditPopup($model,
                                        'DiscussionValidate', $action, ['class' => 'btn btn-tools-secondary','title' => Yii::t('amoscore', 'Modifica'), 'data-pjax' => '0']);
                                    return Html::a(\lispa\amos\core\icons\AmosIcons::show('edit'), $action,
                                        $options);
                                } else {
                                    return '';
                                }
                            }
                        ]
                    ],
                ],
                //  ESPORTAZIONE
                'enableExport' => true,
            ],
            'exportConfig' => [
                'exportEnabled' => true,
                'exportColumns' => [
                    'titolo',
                    [
                        'attribute' => 'created_by',
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Pubblicato Da'),
                        'value' => function($model){
                            $pubblicatoDa = UserProfile::findOne(['user_id' => $model->created_by]);
                            return Html::a($pubblicatoDa->getNomeCognome(), ['/admin/user-profile/view', 'id' => $pubblicatoDa->id ], [
                                'title' => AmosDiscussioni::t('amosnews', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $pubblicatoDa->getNomeCognome()])
                            ]);
                        },
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Data pubblicazione'),
                        'format' => 'datetime',
                    ],
                    'status' => [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            /** @var DiscussioniTopic $model */
                            return $model->hasWorkflowStatus() ? $model->getWorkflowStatus()->getLabel() : '--';
                        }
                    ],
                    [
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Contributi'),
                        'value' => function ($model) {
                            /** @var DiscussioniTopic $model */
                            return $model->getDiscussionComments()->count();
                        }
                    ],
                    'hints',
                    [
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Partecipanti'),
                        'value' => function ($model) {
                            /** @var DiscussioniTopic $model */
                            $risposte = $model->getDiscussionComments();
                            return $risposte->groupBy('created_by')->count();
                        }
                    ],
                    [
                        'label' => AmosDiscussioni::t('amosdiscussioni', 'Ultimo Commento/Risposta'),
                        'format' => 'html',
                        'value' => function ($model) {
                            /** @var DiscussioniTopic $model */
                            if ($model->lastCommentUser) {
                                $ultima_risposta = $model->lastCommentUser->nome . ' ' . $model->lastCommentUser->cognome;
                                $data = '<br>' . Yii::$app->formatter->asDatetime($model->lastCommentDate);
                                return $ultima_risposta . $data;
                            } else {
                                return AmosDiscussioni::t('amosdiscussioni', 'Non ci sono ancora contributi');
                            }
                        }
                    ],
                ]
            ],
            'listView' => [
                'itemView' => '_item'
            ],
            'iconView' => [
                'itemView' => '_icon'
            ],
            'mapView' => [
                'itemView' => '_map'
            ]
        ]
    );
    ?>
</div>
