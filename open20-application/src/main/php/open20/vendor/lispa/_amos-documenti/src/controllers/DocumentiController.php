<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\controllers
 * @category   CategoryName
 */

namespace lispa\amos\documenti\controllers;

use lispa\amos\core\controllers\CrudController;
use lispa\amos\core\forms\CreateNewButtonWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\dashboard\controllers\TabDashboardControllerTrait;
use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\documenti\assets\ModuleDocumentiAsset;
use lispa\amos\documenti\models\Documenti;
use lispa\amos\documenti\models\search\DocumentiSearch;
use lispa\amos\documenti\utility\DocumentsUtility;
use kartik\grid\GridView;
use raoul2000\workflow\base\WorkflowException;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class DocumentiController
 *
 * @property \lispa\amos\documenti\models\Documenti $model
 *
 * @package lispa\amos\documenti\controllers
 */
class DocumentiController extends CrudController
{
    /**
     * Uso il trait per inizializzare la dashboard a tab
     */
    use TabDashboardControllerTrait;

    /**
     * @var string $layout
     */
    public $layout = 'main';

    /**
     * @var AmosDocumenti $documentsModule
     */
    public $documentsModule = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initDashboardTrait();

        $this->setModelObj(new Documenti());
        $this->setModelSearch(new DocumentiSearch());

        ModuleDocumentiAsset::register(Yii::$app->view);

        $grid = [
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt') . Html::tag('p', AmosDocumenti::tHtml('amosdocumenti', 'Tabella')),
                'url' => '?currentView=grid'
            ]
        ];
        $list = [
            'list' => [
                'name' => 'list',
                'label' => AmosIcons::show('view-list') . Html::tag('p', AmosDocumenti::tHtml('amosdocumenti', 'Lista')),
                'url' => '?currentView=list'
            ],
        ];

        if (AmosDocumenti::getInstance()->enableFolders) {
            $availableViews = $grid;
        } else {
            $availableViews = ArrayHelper::merge($grid, $list);
        }
        $this->setAvailableViews($availableViews);

        $this->setUpLayout();

        $this->documentsModule = Yii::$app->getModule(AmosDocumenti::getModuleName());

        parent::init();
    }

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
                            'download-documento-principale',
                            'download',
                            'index',
                            'documenti',
                            'all-documents',
                            'own-interest-documents',
                            'new-document-version',
                            'delete-new-document-version',
                            'list-only'
                        ],
                        'roles' => [
                            'LETTORE_DOCUMENTI',
                            'AMMINISTRATORE_DOCUMENTI',
                            'CREATORE_DOCUMENTI',
                            'FACILITATORE_DOCUMENTI',
                            'VALIDATORE_DOCUMENTI'
                        ]
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'own-documents',
                        ],
                        'roles' => ['CREATORE_DOCUMENTI', 'AMMINISTRATORE_DOCUMENTI', 'FACILITATORE_DOCUMENTI']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'validate-document',
                            'reject-document',
                        ],
                        'roles' => [
                            'AMMINISTRATORE_DOCUMENTI',
                            'FACILITATORE_DOCUMENTI',
                            'FACILITATOR',
                            'DocumentValidateOnDomain',
                            'VALIDATORE_DOCUMNENTI'
                        ]
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'to-validate-documents'
                        ],
                        'roles' => [
                            'VALIDATORE_DOCUMENTI',
                            'FACILITATORE_DOCUMENTI',
                            'AMMINISTRATORE_DOCUMENTI',
                            'DocumentValidateOnDomain'
                        ]
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'admin-all-documents'
                        ],
                        'roles' => ['AMMINISTRATORE_DOCUMENTI']
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
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @param int $id Document id.
     * @return \yii\web\Response
     */
    public function actionValidateDocument($id)
    {
        $this->model = Documenti::findOne($id);
        try {
            $this->model->sendToStatus(Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO);
            $ok = $this->model->save(false);
            if ($ok) {
                Yii::$app->session->addFlash('success', AmosDocumenti::t('amosdocumenti', 'Document validated!'));
            } else {
                Yii::$app->session->addFlash('danger', AmosDocumenti::t('amosdocumenti', '#ERROR_WHILE_VALIDATING_DOCUMENT'));
            }
        } catch (WorkflowException $e) {
            Yii::$app->session->addFlash('danger', AmosDocumenti::t('amosdocumenti', $e->getMessage()));
            return $this->redirect(Url::previous());
        }
        return $this->redirect(Url::previous());
    }

    /**
     * @param int $id Document id.
     * @return \yii\web\Response
     */
    public function actionRejectDocument($id)
    {
        $this->model = Documenti::findOne($id);
        try {
            $this->model->sendToStatus(Documenti::DOCUMENTI_WORKFLOW_STATUS_BOZZA);
            $ok = $this->model->save(false);
            if ($ok) {
                Yii::$app->session->addFlash('success', AmosDocumenti::t('amosdocumenti', 'Document rejected!'));
            } else {
                Yii::$app->session->addFlash('danger', AmosDocumenti::t('amosdocumenti', '#ERROR_WHILE_REJECTING_DOCUMENT'));
            }
        } catch (WorkflowException $e) {
            Yii::$app->session->addFlash('danger', AmosDocumenti::t('amosdocumenti', $e->getMessage()));
            return $this->redirect(Url::previous());
        }
        $this->model->save(false);
        Yii::$app->session->addFlash('success', AmosDocumenti::t('amosdocumenti', 'Document rejected!'));
        return $this->redirect(Url::previous());
    }

    /**
     * Lists all Documenti models.
     * @return mixed
     */
    public function actionIndex($layout = null)
    {
        $parentId = Yii::$app->request->getQueryParam('parentId');
        return $this->redirect(['/documenti/documenti/all-documents', 'parentId' => $parentId]);
    }

    /**
     * Used for set page title and breadcrumbs.
     *
     * @param string $documentiPageTitle Documenti page title (ie. Created by documenti, ...)
     */
    private function setTitleAndBreadcrumbs($documentiPageTitle)
    {
        $this->setNetworkDashboardBreadcrumb();

        $parentId = Yii::$app->request->getQueryParam('parentId');
        if (isset($parentId)) {
            $folder = Documenti::findOne($parentId);
            if (!is_null($folder)) {
                $documentiPageTitle = $folder->getTitle();
            }
        }
        Yii::$app->session->set('previousTitle', $documentiPageTitle);
        Yii::$app->session->set('previousUrl', Url::previous());
        Yii::$app->view->title = $documentiPageTitle;
        Yii::$app->view->params['breadcrumbs'][] = ['label' => $documentiPageTitle];
    }

    public function setNetworkDashboardBreadcrumb()
    {
        /** @var \lispa\amos\cwh\AmosCwh $moduleCwh */
        $moduleCwh = Yii::$app->getModule('cwh');
        $scope = null;
        if (!empty($moduleCwh)) {
            $scope = $moduleCwh->getCwhScope();
        }
        if (!empty($scope)) {
            if (isset($scope['community'])) {
                $communityId = $scope['community'];
                $community = \lispa\amos\community\models\Community::findOne($communityId);
                $dashboardCommunityTitle = AmosDocumenti::t('amosdocumenti', "Dashboard") . ' ' . $community->name;
                $dasbboardCommunityUrl = Yii::$app->urlManager->createUrl(['community/join', 'id' => $communityId]);
                Yii::$app->view->params['breadcrumbs'][] = [
                    'label' => $dashboardCommunityTitle,
                    'url' => $dasbboardCommunityUrl
                ];
            }
        }
    }

    /**
     * Set a view param used in \lispa\amos\core\forms\CreateNewButtonWidget
     */
    private function setCreateNewBtnLabel()
    {
        $parentId = null;
        if (!is_null(Yii::$app->request->getQueryParam('parentId'))) {
            $parentId = Yii::$app->request->getQueryParam('parentId');
        }
        $createNewBtnParams = [
            'createNewBtnLabel' => AmosDocumenti::t('amosdocumenti', 'Aggiungi nuovo documento'),
            'urlCreateNew' => ['/documenti/documenti-wizard/introduction', 'parentId' => $parentId],
            'otherOptions' => ['title' => AmosDocumenti::t('amosdocumenti', 'Aggiungi nuovo documento')]
        ];
        if (AmosDocumenti::getInstance()->enableFolders) {

            $btnBack = '';
            // find Url to navigate previous folder
            if (!is_null($parentId)) {
                $parent = Documenti::findOne($parentId);
                if (!is_null($parent)) {
                    $url = [$this->action->id, 'parentId' => $parent->parent_id];
                    $btnBack = Html::a(AmosDocumenti::tHtml('amosdocumenti', '#btn_back_prev_folder'), $url, ['class' => 'btn btn-secondary']);
                }
            }
            $btnNewFolder = CreateNewButtonWidget::widget([
                'createNewBtnLabel' => AmosDocumenti::t('amosdocumenti', '#btn_new_folder'),
                'urlCreateNew' => ['/documenti/documenti/create', 'isFolder' => true, 'parentId' => $parentId],
                'otherOptions' => ['title' => AmosDocumenti::t('amosdocumenti', '#btn_new_folder')]
            ]);
            $createNewBtnParams = ArrayHelper::merge($createNewBtnParams, [
                'layout' => $btnBack . "{buttonCreateNew}" . $btnNewFolder
            ]);
        }
        Yii::$app->view->params['createNewBtnParams'] = $createNewBtnParams;
    }

    /**
     * This method is useful to set all common params for all list views.
     */
    protected function setListViewsParams()
    {
        $this->setCreateNewBtnLabel();
        Yii::$app->session->set(AmosDocumenti::beginCreateNewSessionKey(), Url::previous());
    }

    /**
     * @param Documenti $model
     * @return mixed|\yii\web\Response
     */
    public function getFormCloseUrl($model)
    {
        if ($this->documentsModule->enableDocumentVersioning && !$model->isNewRecord && !is_null($model->version) && ($model->version > 1)) {
            return ['/documenti/documenti/delete-new-document-version', 'id' => (!is_null($model) ? $model->id : $this->model->id)];
        } else {
            return Yii::$app->session->get('previousUrl');
        }
    }

    /**
     * @param Documenti $model
     * @return string
     */
    public function getFormCloseLabel($model)
    {
        $label = '';
        if ($this->documentsModule->enableDocumentVersioning && !$model->isNewRecord && !is_null($model->version) && ($model->version > 1)) {
            $label = AmosDocumenti::t('amosdocumenti', '#CANCEL_NEW_VERSION');
        }
        return $label;
    }

    /**
     * Displays a single Documenti model.
     *
     * @param integer $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->save()) {
            return $this->redirect(['view', 'id' => $this->model->id, 'idDocumenti' => $id]);
        } else {
            return $this->render('view', ['model' => $this->model]);
        }
    }

    /**
     * Creates a new Documenti model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->setUpLayout('form');
        $this->model = new Documenti();
        $module = \Yii::$app->getModule(AmosDocumenti::getModuleName());
        if($module->hidePubblicationDate) {
            $this->model = new Documenti(['scenario' => Documenti::SCENARIO_CREATE_HIDE_PUBBLICATION_DATE]);
        }
        else {
            $this->model = new Documenti(['scenario' => Documenti::SCENARIO_CREATE]);
        }
        $params = Yii::$app->request->getQueryParams();
        if (isset($params['isFolder'])) {
            $this->model->setScenario(Documenti::SCENARIO_FOLDER);
            $this->model->is_folder = Documenti::IS_FOLDER;
        }
        if (isset($params['parentId'])) {
            $this->model->parent_id = $params['parentId'];
        }
        if ($this->model->load(Yii::$app->request->post())) {
            if ($this->model->validate()) {
                if ($this->model->save()) {
                    Yii::$app->getSession()->addFlash('success', AmosDocumenti::tHtml('amosdocumenti', 'Documenti salvata con successo.'));
                    if($this->model->is_folder){
                        return $this->redirect(['/documenti/documenti/own-interest-documents']);
                    }
                    return $this->redirect(['/documenti/documenti/update', 'id' => $this->model->id]);
                } else {
                    Yii::$app->getSession()->addFlash('danger', AmosDocumenti::tHtml('amosdocumenti', 'Si &egrave; verificato un errore durante il salvataggio'));
                    return $this->render('create', [
                        'model' => $this->model,
                    ]);
                }
            } else {
                Yii::$app->getSession()->addFlash('danger', AmosDocumenti::tHtml('amosdocumenti', 'Modifiche non salvate. Verifica l\'inserimento dei campi'));
            }
        }
        return $this->render('create', [
            'model' => $this->model,
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionNewDocumentVersion($id)
    {
        $this->model = $this->findModel($id);
        $ok = $this->model->makeNewDocumentVersion();
        $url = ['update', 'id' => $this->model->id];
        if (!$ok) {
            $url = Yii::$app->session->get(AmosDocumenti::beginCreateNewSessionKey());
            Yii::$app->getSession()->addFlash('danger', AmosDocumenti::tHtml('amosdocumenti', 'Errore durante la creazione della nuova versione.'));
        }
        return $this->redirect($url);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDeleteNewDocumentVersion($id)
    {
        $this->model = $this->findModel($id);
        $ok = $this->model->deleteNewDocumentVersion();
        if (!$ok) {
            Yii::$app->getSession()->addFlash('danger', AmosDocumenti::tHtml('amosdocumenti', 'Errore durante la cancellazione della nuova versione.'));
        }
        return $this->redirect(Yii::$app->session->get(AmosDocumenti::beginCreateNewSessionKey()));
    }

    /**
     * Updates an existing Documenti model.
     *
     * @param integer $id
     * @param bool|false $backToEditStatus Save the model with status Editing in progress before form rendering
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $backToEditStatus = false)
    {
        Url::remember();

        $this->setUpLayout('form');

        $this->model = $this->findModel($id);
        if ($this->documentIsFolder($this->model)) {
            $this->model->setScenario(Documenti::SCENARIO_FOLDER);
        }else {
            $this->model->setScenario(Documenti::SCENARIO_UPDATE);
        }

        if (Yii::$app->request->post()) {
            if ($this->model->load(Yii::$app->request->post())) {
                if ($this->model->validate()) {
                    if ($this->model->save()) {
                        Yii::$app->getSession()->addFlash('success', AmosDocumenti::tHtml('amosdocumenti', 'Documento aggiornato con successo.'));
                        return $this->redirect(['/documenti/documenti/update', 'id' => $this->model->id]);
                    } else {
                        Yii::$app->getSession()->addFlash('danger', AmosDocumenti::tHtml('amosdocumenti', 'Si &egrave; verificato un errore durante il salvataggio'));
                        return $this->render('create', [
                            'model' => $this->model,
                        ]);
                    }
                } else {
                    Yii::$app->getSession()->addFlash('danger', AmosDocumenti::tHtml('amosdocumenti', 'Modifiche non salvate. Verifica l\'inserimento dei campi'));
                }
            }
        } else {
            if ($backToEditStatus && ($this->model->status != $this->model->getDraftStatus() && !Yii::$app->user->can('DocumentValidate', ['model' => $this->model]))) {
                $this->model->status = $this->model->getDraftStatus();
                $ok = $this->model->save(false);
                if (!$ok) {
                    Yii::$app->getSession()->addFlash('danger', AmosDocumenti::tHtml('amosdocumenti', 'Si &egrave; verificato un errore durante il salvataggio'));
                }
            }
        }

        return $this->render('update', [
            'model' => $this->model,
        ]);
    }

    /**
     * Private method to download a file.
     *
     * @param string $path A path to a file.
     * @param string $file A filename
     * @param array $extensions
     * @param string $titolo
     * @return bool
     */
    private function downloadFile($path, $file, $extensions = [], $titolo = null)
    {
        if (is_file($path)) {
            $file_info = pathinfo($path);
            $extension = $file_info["extension"];

            if (is_array($extensions)) {
                foreach ($extensions as $e) {
                    if ($e === $extension) {
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        $titolo = $titolo ? $titolo : 'Allegato_documenti';
                        header('Content-Disposition: attachment; filename=' . $titolo . '.' . $extension);
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($path));
                        readfile($path);
                        ob_clean();
                        flush();

                        return true; //Yii::$app->response->sendFile($path);
                    }
                }
            }
        }
        return false;
    }

    /**
     * Deletes an existing Documenti model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return \yii\web\Response
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->model = $this->findModel($id);
        $this->model->delete();
        if (!$this->model->getErrors()) {
            Yii::$app->getSession()->addFlash('success', AmosDocumenti::tHtml('amosdocumenti', 'Documento cancellato correttamente.'));
        } else {
            Yii::$app->getSession()->addFlash('danger', AmosDocumenti::tHtml('amosdocumenti', 'Non sei autorizzato a cancellare il documento.'));
        }
        return $this->redirect(Url::previous());
    }

    /**
     * Action to search only for own documents
     *
     * @param int|null $parentId - id of document folder
     * @return string
     */
    public function actionOwnDocuments($parentId = null)
    {
        Url::remember();

        if (!is_null($parentId)) { //set parent Id to filter documents within a folder
            $modelSearch = $this->getModelSearch();
            $modelSearch->parentId = $parentId;
            $this->setModelSearch($modelSearch);
        }

        $this->setDataProvider($this->getModelSearch()->searchOwnDocuments(Yii::$app->request->getQueryParams()));
        $this->setTitleAndBreadcrumbs(AmosDocumenti::t('amosdocumenti', 'Documenti creati da me'));

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt') . Html::tag('p', AmosDocumenti::tHtml('amosdocumenti', 'Tabella')),
                'url' => '?currentView=grid'
            ]
        ]);
        $this->setCurrentView($this->getAvailableView('grid'));
        $this->setListViewsParams();

        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();

        return $this->render('index', [
            'dataProvider' => $this->getDataProvider(),
            'model' => $this->getModelSearch(),
            'currentView' => $this->getCurrentView(),
            'availableViews' => $this->getAvailableViews(),
            'url' => ($this->url) ? $this->url : null,
            'parametro' => ($this->parametro) ? $this->parametro : null
        ]);
    }

    /**
     * Action to search only for own interest documents
     *
     * @param int|null $parentId - id of document folder
     * @return string
     */
    public function actionOwnInterestDocuments($currentView = null, $parentId = null)
    {
        Url::remember();

        if (empty($currentView)) {
            if (!AmosDocumenti::getInstance()->enableFolders) {
                $currentView = 'list';
            } else { //foldering enabled, available gridView only
                $currentView = 'grid';
            }
        }
        if (!is_null($parentId)) { //set parent Id to filter documents within a folder
            $modelSearch = $this->getModelSearch();
            $modelSearch->parentId = $parentId;
            $this->setModelSearch($modelSearch);
        }

        $this->setDataProvider($this->getModelSearch()->searchOwnInterest(Yii::$app->request->getQueryParams()));

        $this->setTitleAndBreadcrumbs(AmosDocumenti::t('amosdocumenti', 'Documenti di mio interesse'));
        $this->setCurrentView($this->getAvailableView($currentView));
        $this->setListViewsParams();

        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();

        return $this->render('index', [
            'dataProvider' => $this->getDataProvider(),
            'model' => $this->getModelSearch(),
            'currentView' => $this->getCurrentView(),
            'availableViews' => $this->getAvailableViews(),
            'url' => ($this->url) ? $this->url : null,
            'parametro' => ($this->parametro) ? $this->parametro : null
        ]);
    }

    /**
     * Action to search to validate documents.
     *
     * @param int|null $parentId - id of document folder
     * @return string
     */
    public function actionToValidateDocuments($parentId = null)
    {
        Url::remember();

        if (!is_null($parentId)) { //set parent Id to filter documents within a folder
            $modelSearch = $this->getModelSearch();
            $modelSearch->parentId = $parentId;
            $this->setModelSearch($modelSearch);
        }

        $this->setDataProvider($this->getModelSearch()->searchToValidateDocuments(Yii::$app->request->getQueryParams()));
        $this->setTitleAndBreadcrumbs(AmosDocumenti::t('amosdocumenti', 'Documenti da validare'));

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt') . Html::tag('p', AmosDocumenti::tHtml('amosdocumenti', 'Tabella')),
                'url' => '?currentView=grid'
            ]
        ]);
        $this->setCurrentView($this->getAvailableView('grid'));
        $this->setListViewsParams();

        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();

        return $this->render('index', [
            'dataProvider' => $this->getDataProvider(),
            'model' => $this->getModelSearch(),
            'currentView' => $this->getCurrentView(),
            'availableViews' => $this->getAvailableViews(),
            'url' => ($this->url) ? $this->url : null,
            'parametro' => ($this->parametro) ? $this->parametro : null
        ]);
    }

    /**
     * Action for search all documenti.
     *
     * @param int|null $parentId - id of document folder
     * @return string
     */
    public function actionAllDocuments($currentView = null, $parentId = null)
    {
        Url::remember();

        if (empty($currentView)) {
            if (!AmosDocumenti::getInstance()->enableFolders) {
                $currentView = 'list';
            } else { //foldering enabled, available gridView only
                $currentView = 'grid';
            }
        }

        if (!is_null($parentId)) { //set parent Id to filter documents within a folder
            $modelSearch = $this->getModelSearch();
            $modelSearch->parentId = $parentId;
            $this->setModelSearch($modelSearch);
        }

        $this->setDataProvider($this->getModelSearch()->searchAll(Yii::$app->request->getQueryParams()));

        $this->setTitleAndBreadcrumbs(AmosDocumenti::t('amosdocumenti', 'Tutti i documenti'));
        $this->setCurrentView($this->getAvailableView($currentView));
        $this->setListViewsParams();

        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();

        return $this->render('index', [
            'dataProvider' => $this->getDataProvider(),
            'model' => $this->getModelSearch(),
            'currentView' => $this->getCurrentView(),
            'availableViews' => $this->getAvailableViews(),
            'url' => ($this->url) ? $this->url : null,
            'parametro' => ($this->parametro) ? $this->parametro : null
        ]);
    }

    /**
     * Get all the documents without any visibility/status filters
     *
     * @param null $currentView
     * @param int|null $parentId - id of document folder
     * @return string
     */
    public function actionAdminAllDocuments($currentView = null, $parentId = null)
    {
        Url::remember();

        if (empty($currentView)) {
            if (!AmosDocumenti::getInstance()->enableFolders) {
                $currentView = 'list';
            } else { //foldering enabled, available gridView only
                $currentView = 'grid';
            }
        }
        if (!is_null($parentId)) { //set parent Id to filter documents within a folder
            $modelSearch = $this->getModelSearch();
            $modelSearch->parentId = $parentId;
            $this->setModelSearch($modelSearch);
        }
        $this->setDataProvider($this->getModelSearch()->searchAdminAll(Yii::$app->request->getQueryParams()));

        $this->setTitleAndBreadcrumbs(AmosDocumenti::t('amosdocumenti', 'Amministra documenti'));
        $this->setCurrentView($this->getAvailableView($currentView));
        $this->setListViewsParams();

        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();

        return $this->render('index', [
            'dataProvider' => $this->getDataProvider(),
            'model' => $this->getModelSearch(),
            'currentView' => $this->getCurrentView(),
            'availableViews' => $this->getAvailableViews(),
            'url' => ($this->url) ? $this->url : null,
            'parametro' => ($this->parametro) ? $this->parametro : null
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
                $this->layout = '@vendor/lispa/amos-core/views/layouts/' . (!empty($layout) ? $layout : $this->layout);
            }
            return true;
        }
        return true;
    }

    /**
     * If a given document model is a folder or not
     * @param Documenti $model
     * @return bool
     */
    public function documentIsFolder($model)
    {
        return (isset($model->is_folder) && $model->is_folder);
    }


    /**
     * Render the sub-table of version, it's callded with ajax
     * @return string
     */
    public function actionListOnly()
    {
        $expandRowKey = \Yii::$app->request->post('expandRowKey');
        $actionId = $this->action->id;
        $hidePubblicationDate = $this->documentsModule->hidePubblicationDate;


        \Yii::$app->request->setQueryParams(['parent_id' => $expandRowKey]);

        $dataProvider = $this->getModelSearch()->searchVersions(\Yii::$app->request->getQueryParams());
//        return ($dataProvider->query->createCommand()->rawSql);
        $dataProvider->sort = false;
        $canUpdate = Yii::$app->user->can('DOCUMENTI_UPDATE', ['model' => $this->model]);
        $btnCreate = '';
        if($canUpdate) {
            $btnCreate = CreateNewButtonWidget::widget([
                'model' => $this->model,
                'createNewBtnLabel' => AmosDocumenti::t('amosdocumenti', 'Create new version'),
                'urlCreateNew' => ['/documenti/documenti/new-document-version', 'id' => $expandRowKey],
                'btnClasses' => 'btn btn-success pull-right',
                'otherOptions' => ['title'=> AmosDocumenti::t('amosdocumenti', 'Create new version')]
//                            'checkPermWithNewMethod' => true
            ]);
        }

        try {
            return GridView::widget([
                'id' => 'product-gridview',
                'dataProvider' => $dataProvider,
                'responsive' => true,
                'export' => false,
                'pjax' => true,
                'pjaxSettings' => [
                    'options' => [
                        'id' => 'product-grid',
                        'timeout' => (isset(\Yii::$app->params['timeout']) ? \Yii::$app->params['timeout'] : 1000),
                        'enablePushState' => false
                    ]
                ],
                'columns' => [
                    [
                        'label' => AmosDocumenti::t('amosdocumenti', '#type'),
                        'format' => 'html',
                        'value' => function ($model) {
                            $icon = DocumentsUtility::getDocumentIcon($model, true);
                            return AmosIcons::show($icon, [], 'dash');
                        },
                    ],
                    [
                        'attribute' => 'titolo',
                        'format' => 'html',
                        'value' => function ($model) use ($actionId) {
                            /** @var Documenti $model */
                            $title = $model->titolo;
                            if ($model->is_folder) {
                                $url = [$actionId, 'parentId' => $model->id];
                            } else {
                                $url = $model->getDocumentMainFile()->getUrl();
                            }
                            return Html::a($title, $url);
                        }
                    ],
                    [
                        'attribute' => 'updatedUserProfile',
                        'label' => AmosDocumenti::t('amosdocumenti', '#updated_by'),
                        'value' => function($model){
                            return Html::a($model->updatedUserProfile->nomeCognome, ['/admin/user-profile/view', 'id' => $model->updatedUserProfile->id ], [
                                'title' => AmosDocumenti::t('amosdocumenti', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $model->updatedUserProfile->nomeCognome])
                            ]);
                        },
                        'format' => 'html'
                    ],
                    'version',
                    'data_pubblicazione' => [
                        'attribute' => 'data_pubblicazione',
                        'value' => function ($model) {
                            /** @var Documenti $model */
                            return (is_null($model->data_pubblicazione)) ? 'Subito' : Yii::$app->formatter->asDate($model->data_pubblicazione);
                        },
                        'visible' => !$hidePubblicationDate
                    ],
                    [
                        'class' => 'lispa\amos\core\views\grid\ActionColumn',
                        'template' => '{view}',
                    ],
                ],
                'panelHeadingTemplate' => '<div class="pull-right">
                    </div>
                    <h3 class="panel-title">
                        {heading}
                    </h3>
                    <div class="clearfix"></div>',
                'panel' => [
                    'before' => false,
                    'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i>&nbsp;' . AmosDocumenti::t('amosdocumenti',
                            'Old versions') . '</h3>'
                        . $btnCreate,
                    'type' => 'success',
                    'after' => false,
                    'footer' => false,
                ],
            ]);
        } catch (\Exception $e) {
            pr($e->getTraceAsString());
            return $e->getMessage();
        }
    }
}
