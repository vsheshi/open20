<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\controllers\base;

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\assets\AmosCommunityAsset;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\community\models\search\CommunitySearch;
use lispa\amos\core\controllers\CrudController;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\dashboard\controllers\TabDashboardControllerTrait;
use Yii;
use yii\helpers\Url;

/**
 * Class CommunityController
 * CommunityController implements the CRUD actions for Community model.
 * @package lispa\amos\community\controllers\base
 */
class CommunityController extends CrudController
{
    /**
     * Uso il trait per inizializzare la dashboard a tab
     */
    use TabDashboardControllerTrait;

    /**
     * @var string $layout
     */
    public $layout = 'list';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initDashboardTrait();

        $this->setModelObj(AmosCommunity::instance()->createModel('Community'));
        $this->setModelSearch(new CommunitySearch());

        AmosCommunityAsset::register(Yii::$app->view);

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt').Html::tag('p',AmosCommunity::t('amoscommunity', 'Table')),
                'url' => '?currentView=grid'
            ],
            'icon' => [
                'name' => 'icon',
                'label' => AmosIcons::show('view-list').Html::tag('p',AmosCommunity::t('amoscommunity', 'Card')),
                'url' => '?currentView=icon'
            ],
            /* 'list' => [
              'name' => 'list',
              'label' => AmosCommunity::t('amoscommunity', '{iconaLista}'.Html::tag('p',AmosCommunity::t('amoscommunity', 'Lista')), [
              'iconaLista' => AmosIcons::show('view-list')
              ]),
              'url' => '?currentView=list'
              ],
              'map' => [
              'name' => 'map',
              'label' => AmosCommunity::t('amoscommunity', '{iconaMappa}'.Html::tag('p',AmosCommunity::t('amoscommunity', 'Mappa')), [
              'iconaMappa' => AmosIcons::show('map-alt')
              ]),
              'url' => '?currentView=map'
              ],
              'calendar' => [
              'name' => 'calendar',
              'intestazione' => '', //codice HTML per l'intestazione che verrà caricato prima del calendario,
                                    //per esempio si può inserire una funzione $model->getHtmlIntestazione() creata ad hoc
              'label' => AmosCommunity::t('amoscommunity', '{iconaCalendario}'.Html::tag('p',AmosCommunity::t('amoscommunity', 'Calendario')), [
              'iconaCalendario' => AmosIcons::show('calendar')
              ]),
              'url' => '?currentView=calendar'
              ], */
        ]);

        parent::init();
        $this->setUpLayout();
    }

    /**
     * Lists all Community models.
     * @return mixed
     */
    public function actionIndex($layout = NULL)
    {
        Url::remember();
        $this->setDataProvider($this->getModelSearch()->searchAll(Yii::$app->request->getQueryParams()));
        return parent::actionIndex();
    }

    /**
     * Displays a single Community model.
     * @param integer $id
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id, $tabActive = null)
    {
        Url::remember();
        $this->setUpLayout('main');
        $this->model = $this->findModel($id);
        if ($this->model->load(Yii::$app->request->post()) && $this->model->save()) {
            return $this->redirect(['view', 'id' => $this->model->id]);
        } else {
            return $this->render('view', [
                'model' => $this->model,
                'tabActive' => $tabActive,
            ]);
        }
    }


    /**
     * Creates a new Community model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->setUpLayout('form');
        $model = new Community;
        $model->context = Community::className();

        $parentId = Yii::$app->request->getQueryParam('parentId');
        if(!is_null($parentId)){
            $model->parent_id = $parentId;
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (Yii::$app->getModule('community')->bypassWorkflow) {
                $model->validated_once = 1;
            }
            if ($model->save()) {
                //the loggerd user creating community will be automatically a participant of the community with role community manager
                $loggedUserId = Yii::$app->getUser()->getId();
                $userCommunity = new CommunityUserMm();
                $userCommunity->community_id = $model->id;
                $userCommunity->user_id = $loggedUserId;
                $userCommunity->status = CommunityUserMm::STATUS_ACTIVE;
                $userCommunity->role = CommunityUserMm::ROLE_COMMUNITY_MANAGER;
                // add cwh auth-assignment permission for community/user
                $model->setCwhAuthAssignments($userCommunity);
                $ok = $userCommunity->save(false);
                Yii::$app->getSession()->addFlash('success', AmosCommunity::t('amoscommunity', 'Community created successfully.'));
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->getSession()->addFlash('danger',
                    AmosCommunity::t('amoscommunity', 'Community not created. Please, check data entry.'));
                return $this->render('create', [
                    'model' => $model,
                    'fid' => null,
                    'dataField' => null,
                    'dataEntity' => null,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'fid' => NULL,
                'dataField' => NULL,
                'dataEntity' => NULL,
            ]);
        }
    }

    /**
     * Creates a new Community model by ajax request.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateAjax($fid, $dataField)
    {
        $this->setUpLayout('form');
        $model = new Community;

        if (\Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save()) {
                    //Yii::$app->getSession()->addFlash('success', AmosCommunity::t('amoscommunity', 'Elemento creato correttamente.'));
                    return json_encode($model->toArray());
                } else {
                    //Yii::$app->getSession()->addFlash('danger', AmosCommunity::t('amoscommunity', 'Elemento non creato, verificare i dati inseriti.'));
                    return $this->renderAjax('_formAjax', [
                        'model' => $model,
                        'fid' => $fid,
                        'dataField' => $dataField
                    ]);
                }
            }
        } else {
            return $this->renderAjax('_formAjax', [
                'model' => $model,
                'fid' => $fid,
                'dataField' => $dataField
            ]);
        }
    }

    /**
     * Updates an existing Community model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @param bool $visibleOnEdit
     * @param string $tabActive
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id, $visibleOnEdit = false, $tabActive = null)
    {

        $this->setUpLayout('form');
        /** @var Community $model */
        $model = $this->findModel($id);
        $communityModule = Yii::$app->getModule('community');

        if ($model->load(Yii::$app->request->post())) {
            if(!$communityModule->bypassWorkflow && $model->backToEdit && $model->status != Community::COMMUNITY_WORKFLOW_STATUS_DRAFT && $model->status != Community::COMMUNITY_WORKFLOW_STATUS_TO_VALIDATE){
                if($model->validated_once) {
                    $model->status = Community::COMMUNITY_WORKFLOW_STATUS_DRAFT;
                }
            }
            if(!empty($model->status) && $model->status == Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED){
                $model->validated_once = 1;
            }

            if ($model->validate()) {
                if ($model->save()) {
                    Yii::$app->getSession()->addFlash('success', AmosCommunity::t('amoscommunity', 'Community updated successfully.'));
                } else {
                    Yii::$app->getSession()->addFlash('danger', AmosCommunity::t('amoscommunity', 'Community not updated. Please, check data entry.'));
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'fid' => NULL,
            'dataField' => NULL,
            'dataEntity' => NULL,
            'visibleOnEdit' => $visibleOnEdit,
            'tabActive' => $tabActive,
        ]);
    }

    /**
     * Deletes an existing Community model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * in Community model beforeDelete is overwritten to allow deletion of related models
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $isAjaxRequest = false)
    {
        $model = $this->findModel($id);
        if ($model) {
            try{
                $model->delete();
                if($isAjaxRequest){
                    return [
                        'success' => true,
                    ];
                } else {
                    Yii::$app->getSession()->addFlash('success', AmosCommunity::t('amoscommunity', 'Community deleted successfully.'));
                }
            } catch (\Exception $exception) {
                if($isAjaxRequest){
                    return [
                        'success' => false,
                        'message' => AmosCommunity::t('amoscommunity', $exception->getMessage()),
                    ];
                } else {
                    Yii::$app->getSession()->addFlash('danger', AmosCommunity::t('amoscommunity', $exception->getMessage()));
                }
            }
        } else {
            if($isAjaxRequest){
                return [
                    'success' => false,
                    'message' => AmosCommunity::t('amoscommunity', 'Community not found.'),
                ];
            } else {
                Yii::$app->getSession()->addFlash('danger', AmosCommunity::t('amoscommunity', 'Community not found.'));
            }
        }

        //return $this->redirect(['index']);
        return $this->redirect('/community/community/my-communities');
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
