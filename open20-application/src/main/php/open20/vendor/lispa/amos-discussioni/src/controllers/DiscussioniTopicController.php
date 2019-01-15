<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

namespace lispa\amos\discussioni\controllers;

use lispa\amos\core\controllers\CrudController;
use lispa\amos\core\helpers\BreadcrumbHelper;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\dashboard\controllers\TabDashboardControllerTrait;
use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\discussioni\models\DiscussioniTopic;
use lispa\amos\discussioni\models\search\DiscussioniTopicSearch;
use lispa\amos\discussioni\assets\ModuleDiscussioniInterfacciaAsset;
use raoul2000\workflow\base\WorkflowException;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class DiscussioniTopicController
 * DiscussioniTopicController implements the CRUD actions for DiscussioniTopic model.
 * @package lispa\amos\discussioni\controllers
 */
class DiscussioniTopicController extends CrudController
{
    use TabDashboardControllerTrait;

    /**
     * @var string $layout
     */
    public $layout = 'list';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'partecipa',
                            'index',
                            'all-discussions',
                            'own-interest-discussions'
                        ],
                        'roles' => ['LETTORE_DISCUSSIONI', 'AMMINISTRATORE_DISCUSSIONI', 'CREATORE_DISCUSSIONI', 'FACILITATORE_DISCUSSIONI', 'VALIDATORE_DISCUSSIONI']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'created-by',
                        ],
                        'roles' => ['CREATORE_DISCUSSIONI', 'AMMINISTRATORE_DISCUSSIONI', 'FACILITATORE_DISCUSSIONI']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'validate-discussion',
                            'reject-discussion',
                        ],
                        'roles' => ['AMMINISTRATORE_DISCUSSIONI', 'FACILITATORE_DISCUSSIONI', 'FACILITATOR', 'DiscussionValidateOnDomain', 'VALIDATORE_DISCUSSIONI']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'to-validate-discussions'
                        ],
                        'roles' => ['VALIDATORE_DISCUSSIONI', 'FACILITATORE_DISCUSSIONI', 'AMMINISTRATORE_DISCUSSIONI', 'DiscussionValidateOnDomain']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'admin-all-discussions'
                        ],
                        'roles' => ['AMMINISTRATORE_DISCUSSIONI']
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get']
                ]
            ]
        ]);
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initDashboardTrait();
        
        $this->setModelObj(new DiscussioniTopic());
        $this->setModelSearch(new DiscussioniTopicSearch());

        ModuleDiscussioniInterfacciaAsset::register(Yii::$app->view);
        
        $this->setAvailableViews([
            'list' => [
                'name' => 'list',
                'label' => AmosDiscussioni::t('amosdiscussioni', '{iconaLista}' . Html::tag('p', AmosDiscussioni::t('amosdiscussioni', 'List')), [
                    'iconaLista' => AmosIcons::show('view-list')
                ]),
                'url' => '?currentView=list'
            ],
            /*'icon' => [
                'name' => 'icon',
                'label' => AmosDiscussioni::t('amosdiscussioni', '{iconaElenco}'.Html::tag('p',AmosDiscussioni::t('amosdiscussioni', 'Icon')), [
                    'iconaElenco' => AmosIcons::show('view-grid')
                ]),
                'url' => '?currentView=icon'
            ], */
            'grid' => [
                'name' => 'grid',
                'label' => AmosDiscussioni::t('amosdiscussioni', '{iconaTabella}' . Html::tag('p', AmosDiscussioni::t('amosdiscussioni', 'Table')), [
                    'iconaTabella' => AmosIcons::show('view-list-alt')
                ]),
                'url' => '?currentView=grid'
            ],
            /* 'map' => [
                'name' => 'map',
                'label' => AmosDiscussioni::t('amosdiscussioni', '{iconaMappa}'.Html::tag('p',AmosDiscussioni::t('amosdiscussioni', 'Map')), [
                    'iconaMappa' => AmosIcons::show('map-alt')
                ]),
                'url' => '?currentView=map'
            ], */
        ]);
        
        parent::init();
        $this->setUpLayout();
    }
    
    public function actionIndex($layout = NULL)
    {
        return $this->redirect(['/discussioni/discussioni-topic/all-discussions']);
        
        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();
        
        //se il layout di default non dovesse andar bene si può aggiuntere il layout desiderato
        //in questo modo nel controller "return parent::actionIndex($this->layout);"
        if ($layout) {
            $this->setUpLayout($layout);
        }
        
        Url::remember();
        
        $this->setDataProvider($this->getModelSearch()->searchAll(Yii::$app->request->getQueryParams()));
        
        $this->setListViewsParams();
        $this->setTitleAndBreadcrumbs(AmosDiscussioni::t('amosdiscussioni', 'Tutte le discussioni'));
        
        return $this->render('index', [
            'dataProvider' => $this->getDataProvider(),
            'model' => $this->getModelSearch(),
            'currentView' => $this->getCurrentView(),
            'availableViews' => $this->getAvailableViews(),
        ]);
    }
    
    /**
     * @param int $id Discussion id.
     * @return \yii\web\Response
     */
    public function actionValidateDiscussion($id)
    {
        $disussione = DiscussioniTopic::findOne($id);
        try {
            $disussione->sendToStatus(DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_ATTIVA);
            $ok = $disussione->save(false);
            if ($ok) {
                Yii::$app->session->addFlash('success', AmosDiscussioni::t('amosdiscussioni', 'Discussion validated!'));
            } else {
                Yii::$app->session->addFlash('danger', AmosDiscussioni::t('amosdiscussioni', '#ERROR_WHILE_VALIDATING_DISCUSSION'));
            }
        } catch (WorkflowException $e) {
            Yii::$app->session->addFlash('danger', AmosDiscussioni::t('amosdiscussioni', $e->getMessage()));
            return $this->redirect(Url::previous());
        }
        return $this->redirect(Url::previous());
    }
    
    /**
     * @param int $id Discussion id.
     * @return \yii\web\Response
     */
    public function actionRejectDiscussion($id)
    {
        $disussione = DiscussioniTopic::findOne($id);
        try {
            $disussione->sendToStatus(DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_BOZZA);
            $ok = $disussione->save(false);
            if ($ok) {
                Yii::$app->session->addFlash('success', AmosDiscussioni::t('amosdiscussioni', 'Discussion rejected!'));
            } else {
                Yii::$app->session->addFlash('danger', AmosDiscussioni::t('amosdiscussioni', '#ERROR_WHILE_REJECTING_DISCUSSION'));
            }
        } catch (WorkflowException $e) {
            Yii::$app->session->addFlash('danger', AmosDiscussioni::t('amosdiscussioni', $e->getMessage()));
            return $this->redirect(Url::previous());
        }
        return $this->redirect(Url::previous());
    }
    
    /**
     * Set a view param used in \lispa\amos\core\forms\CreateNewButtonWidget
     */
    private function setCreateNewBtnLabel()
    {
        Yii::$app->view->params['createNewBtnParams'] = [
            'createNewBtnLabel' => AmosDiscussioni::t('amosdiscussioni', 'Add new discussion'),
            'urlCreateNew' => ['/discussioni/discussioni-topic/create']
        ];
    }
    
    /**
     * This method is useful to set all common params for all list views.
     */
    protected function setListViewsParams()
    {
        $this->setCreateNewBtnLabel();
        Yii::$app->session->set(AmosDiscussioni::beginCreateNewSessionKey(), Url::previous());
    }
    
    /**
     * Used for set page title and breadcrumbs.
     * @param string $disussionePageTitle Discussions page title (ie. Created by discussions, ...)
     */
    private function setTitleAndBreadcrumbs($disussionePageTitle)
    {
        $this->setNetworkDashboardBreadcrumb();
        Yii::$app->session->set('previousTitle', $disussionePageTitle);
        Yii::$app->session->set('previousUrl', Url::previous());
        Yii::$app->view->title = $disussionePageTitle;
        Yii::$app->view->params['breadcrumbs'][] = ['label' => $disussionePageTitle];
    }
    
    public function setNetworkDashboardBreadcrumb()
    {
        /** @var \lispa\amos\cwh\AmosCwh $moduleCwh */
        $moduleCwh = Yii::$app->getModule('cwh');
        $scope = NULL;
        if (!empty($moduleCwh)) {
            $scope = $moduleCwh->getCwhScope();
        }
        if (!empty($scope)) {
            if (isset($scope['community'])) {
                $communityId = $scope['community'];
                $community = \lispa\amos\community\models\Community::findOne($communityId);
                $dashboardCommunityTitle = AmosDiscussioni::t('amosdiscussioni', "Dashboard") . ' ' . $community->name;
                $dasbboardCommunityUrl = Yii::$app->urlManager->createUrl(['community/join', 'id' => $communityId]);
                Yii::$app->view->params['breadcrumbs'][] = ['label' => $dashboardCommunityTitle, 'url' => $dasbboardCommunityUrl];
            }
        }
    }
    
    public function actionCreatedBy()
    {
        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();
        
        Url::remember();
        
        $this->setDataProvider($this->getModelSearch()->searchCreatedBy(Yii::$app->request->getQueryParams()));
        
        $this->setListViewsParams();
        $this->setTitleAndBreadcrumbs(AmosDiscussioni::t('amosdiscussioni', 'Discussioni create da me'));
        
        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosDiscussioni::t('amosdiscussioni', '{iconaTabella}' . Html::tag('p', AmosDiscussioni::t('amosdiscussioni', 'Table')), [
                    'iconaTabella' => AmosIcons::show('view-list-alt')
                ]),
                'url' => '?currentView=grid'
            ]
        ]);
        $this->setCurrentView($this->getAvailableView('grid'));
        
        return parent::actionIndex();
    }
    
    public function actionAllDiscussions()
    {
        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();
        
        Url::remember();
        
        $this->setDataProvider($this->getModelSearch()->searchAll(Yii::$app->request->getQueryParams()));
        
        $this->setListViewsParams();
        $this->setTitleAndBreadcrumbs(AmosDiscussioni::t('amosdiscussioni', 'Tutte le discussioni'));
        
        return parent::actionIndex();
    }

    /**
     * @return string
     */
    public function actionAdminAllDiscussions()
    {
        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();

        Url::remember();

        $this->setDataProvider($this->getModelSearch()->searchAdminAll(Yii::$app->request->getQueryParams()));

        $this->setListViewsParams();
        $this->setTitleAndBreadcrumbs(AmosDiscussioni::t('amosdiscussioni', 'Amministra discussioni'));

        return parent::actionIndex();
    }

    /**
     * @return string
     */
    public function actionToValidateDiscussions()
    {
        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();
        
        Url::remember();
        
        $this->setDataProvider($this->getModelSearch()->searchToValidate(Yii::$app->request->getQueryParams()));
        
        $this->setListViewsParams();
        $this->setTitleAndBreadcrumbs(AmosDiscussioni::t('amosdiscussioni', 'Discussioni da validare'));
        
        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosDiscussioni::t('amosdiscussioni', '{iconaTabella}' . Html::tag('p', AmosDiscussioni::t('amosdiscussioni', 'Table')), [
                    'iconaTabella' => AmosIcons::show('view-list-alt')
                ]),
                'url' => '?currentView=grid'
            ]
        ]);
        $this->setCurrentView($this->getAvailableView('grid'));
        
        return parent::actionIndex();
    }
    
    public function actionOwnInterestDiscussions()
    {
        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();
        
        Url::remember();
        
        $this->setDataProvider($this->getModelSearch()->searchOwnInterest(Yii::$app->request->getQueryParams()));
        
        $this->setListViewsParams();
        $this->setTitleAndBreadcrumbs(AmosDiscussioni::t('amosdiscussioni', 'Discussioni di mio interesse'));
        
        return parent::actionIndex();
    }
    
    /**
     * Displays a single DiscussioniTopic model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        /** @var DiscussioniTopic $model */
        $model = $this->findModel($id);
        
        ///////////// restituisce in view la lista dei TAG Se uno vuole.........
        $tagModule = \Yii::$app->getModule('tag');
        if (isset($tagModule) && in_array(DiscussioniTopic::className(), $tagModule->modelsEnabled)) {
            $tagInteressiTutti = \lispa\amos\tag\models\Tag::find()->where('root IN (1)')->addOrderBy('root, lft')->asArray()->all();
            $tagInteressi = Array();
            
            $tagIdInteressi = explode(",", $model->tagValues1);
            foreach ($tagInteressiTutti as $value) {
                if (in_array($value['id'], $tagIdInteressi)) {
                    $tagInteressi[] = $value;
                }
            }
            
            $tagAmbitoLavTutti = \lispa\amos\tag\models\Tag::find()->where('root IN (10)')->addOrderBy('root, lft')->asArray()->all();
            $tagAmbitoLav = [];
            
            $tagIdAmbitoLav = explode(",", $model->tagValues2);
            foreach ($tagAmbitoLavTutti as $value) {
                if (in_array($value['id'], $tagIdAmbitoLav)) {
                    $tagAmbitoLav[] = $value;
                }
            }
        }
        ////////// Fine...
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'interessi' => $tagInteressi, 'ambitilav' => $tagAmbitoLav]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }
    
    /**
     * Creates a new DiscussioniTopic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->setUpLayout('form');
        $model = new DiscussioniTopic();
        $this->model = $model;
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::$app->getSession()->addFlash('success', AmosDiscussioni::t('amosdiscussioni', 'Discussione salvata con successo.'));
                    return $this->redirect(Yii::$app->session->get('previousUrl'));
                } else {
                    Yii::$app->getSession()->addFlash('danger', AmosDiscussioni::t('amosdiscussioni', 'Si &egrave; verificato un errore durante il salvataggio'));
                }
            } else {
                Yii::$app->getSession()->addFlash('danger', AmosDiscussioni::t('amosdiscussioni', 'Modifiche non salvate. Verifica l\'inserimento dei campi'));
            }
        }
        
        return $this->render('create', [
            'model' => $model
        ]);
    }
    
    /**
     * Updates an existing DiscussioniTopic model.
     *
     * @param integer $id
     * @param bool|false $backToEditStatus Save the model with status Editing in progress before form rendering
     * @return mixed
     */
    public function actionUpdate($id, $backToEditStatus = false)
    {
        $this->setUpLayout('form');
        
        /** @var DiscussioniTopic $model */
        $model = $this->findModel($id);
        
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            
            if ($model->validate()) {
                if ($model->save()) {
                    Yii::$app->getSession()->addFlash('success', AmosDiscussioni::t('amosdiscussioni', 'Modifiche salvate con successo.'));
                    return $this->redirect(Yii::$app->session->get('previousUrl'));
                } else {
                    Yii::$app->getSession()->addFlash('danger', AmosDiscussioni::t('amosdiscussioni', 'Si &egrave; verificato un errore durante il salvataggio'));
                }
            } else {
                Yii::$app->getSession()->addFlash('danger', AmosDiscussioni::t('amosdiscussioni', 'Modifiche non salvate. Verifica l\'inserimento dei campi'));
            }
        } else {
            if ($backToEditStatus && ($model->status != $model->getDraftStatus() && !Yii::$app->user->can('DiscussionValidate', ['model' => $model]))) {
                $model->status = $model->getDraftStatus();
                $ok = $model->save(false);
                if (!$ok) {
                    Yii::$app->getSession()->addFlash('danger', AmosDiscussioni::t('amosdiscussioni', 'Si &egrave; verificato un errore durante il salvataggio'));
                }
            }
        }
        
        return $this->render('update', [
            'model' => $model
        ]);
    }
    
    /**
     * Deletes an existing DiscussioniTopic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->addFlash('success', AmosDiscussioni::t('amosdiscussioni', 'Discussione cancellata correttamente.'));
        return $this->redirect(BreadcrumbHelper::lastCrumbUrl());
    }
    
    public function actionPartecipa($id)
    {
        $this->setUpLayout('main');
        Url::remember();
        
        /** @var DiscussioniTopic $model */
        $model = $this->findModel($id);
        $model->hints++;
        $model->save(FALSE);
        
        $listaAllegati = $model->getdiscussionsAttachments();  // TODO: possibile bug perché la find non ha parametri quindi qui sta cercando tutto.
//        $listaAllegati = DiscussioniAllegati::find()->andWhere(['discussioni_topic_id' => $id])->asArray()->all();  // TODO: correzione alla riga sopra ma tutto da testare. Per ora commentato.
        $ultimaRisposta = $model->getDiscussionComments()->orderBy('id DESC')->one();
        
        $query = $model->getDiscussionComments()->orderBy('id DESC');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->setPageSize(5);
        $discussioniRisposte = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        return $this->render('partecipa', [
            'model' => $model,
            'discussioniRisposte' => $discussioniRisposte,
            'pages' => $pages,
            'ultimaRisposta' => $ultimaRisposta,
            'listaAllegati' => $listaAllegati,
        ]);
    }

    /**
     * @param null $layout
     * @return bool
     */
    public function setUpLayout($layout = null)
    {
        if ($layout === false) {
            $this->layout = false;
            return true;
        }
        $this->layout = (!empty($layout)) ? $layout : $this->layout;
        $module = \Yii::$app->getModule('layout');
        if (empty($module)) {
            if (strpos($this->layout, '@') === false) {
                $this->layout = '@vendor/lispa/amos-core/views/layouts/'.(!empty($layout) ? $layout : $this->layout);
            }
            return true;
        }
        return true;
    }
}
