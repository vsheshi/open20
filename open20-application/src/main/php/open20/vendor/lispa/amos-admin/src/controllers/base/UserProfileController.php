<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\controllers\base
 * @category   CategoryName
 */

namespace lispa\amos\admin\controllers\base;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\controllers\CrudController;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\user\User;
use lispa\amos\dashboard\controllers\TabDashboardControllerTrait;
use lispa\amos\notificationmanager\AmosNotify;
use lispa\amos\notificationmanager\widgets\NotifyFrequencyWidget;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class UserProfileController
 * UserProfileController implements the CRUD actions for UserProfile model.
 *
 * @property \lispa\amos\admin\models\UserProfile $model
 *
 * @package lispa\amos\admin\controllers\base
 */
class UserProfileController extends CrudController
{
    use TabDashboardControllerTrait;

    /**
     * @var string $layout
     */
    public $layout = 'list';

    // La utilizzo per settare il parametri al render anche da classi ereditate.
    // così anche loro potranno aggiungere parametri al render per le viste
    // caso di update
    public $updateParamsRender;
    
    // caso di create
    public $createParamsRender;
    
    //campo di appoggio per poter gestire il dato anche da classi ereditanti
    public $forzaListaRuoli;
    
    protected $gridView = null;
    protected $iconView = null;
    protected $listView = null;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initDashboardTrait();
        
        $this->setModelObj(AmosAdmin::instance()->createModel('UserProfile'));
        $this->setModelSearch(AmosAdmin::instance()->createModel('UserProfileSearch'));
        
        $this->gridView = [
            'name' => 'grid',
            'label' => AmosAdmin::t('amosadmin', '{iconaTabella}' . Html::tag('p', AmosAdmin::t('amosadmin', 'Tabella')), [
                'iconaTabella' => AmosIcons::show('view-list-alt')
            ]),
            'url' => '?currentView=grid'
        ];
        
        $this->iconView = [
            'name' => 'icon',
            'label' => AmosAdmin::t('amosadmin', '{iconaElenco}' . Html::tag('p', AmosAdmin::t('amosadmin', 'Icone')), [
                'iconaElenco' => AmosIcons::show('grid')
            ]),
            'url' => '?currentView=icon'
        ];
        
        $this->listView = [
            'name' => 'list',
            'label' => AmosAdmin::t('amosadmin', '{iconaLista}' . Html::tag('p', AmosAdmin::t('amosadmin', 'Lista')), [
                'iconaLista' => AmosIcons::show('view-list')
            ]),
            'url' => '?currentView=list'
        ];
        
        $this->setAvailableViews([
            'grid' => $this->gridView,
            'icon' => $this->iconView,
            'list' => $this->listView,
            /*
            'map' => [
                'name' => 'map',
                'label' => AmosAdmin::t('amosadmin', '{iconaMappa}' . Html::tag('p', AmosAdmin::t('amosadmin', 'Mappa')), [
                    'iconaMappa' => AmosIcons::show('map-alt')
                ]),
                'url' => '?currentView=map'
            ],
            'calendar' => [
                'name' => 'calendar',
                'label' => AmosAdmin::t('amosadmin', '{iconaCalendario}' . Html::tag('p', AmosAdmin::t('amosadmin', 'Calendario')), [
                    'iconaCalendario' => AmosIcons::show('calendar')
                ]),
                'url' => '?currentView=calendar'
            ],
            */
        ]);
        
        parent::init();
        $this->setUpLayout();
    }
    
    /**
     * Set a view param used in \lispa\amos\core\forms\CreateNewButtonWidget
     */
    protected function setCreateNewBtnParams()
    {
        Yii::$app->view->params['createNewBtnParams'] = [
            'createNewBtnLabel' => AmosAdmin::t('amosadmin', 'Add new user')
        ];
    }
    
    /**
     * Used for set page title and breadcrumbs.
     * @param string $pageTitle
     */
    public function setTitleAndBreadcrumbs($pageTitle)
    {
        Yii::$app->view->title = $pageTitle;
        Yii::$app->view->params['breadcrumbs'] = [
            ['label' => $pageTitle]
        ];
    }
    
    /**
     * Used for set lists view params.
     */
    public function setListsViewParams()
    {
        Yii::$app->session->set('previousUrl', Url::previous());
    }
    
    /**
     * Return an array with the values used in boolean fields.
     * @return array
     */
    public function getBooleanFieldsValues()
    {
        return [
            UserProfile::BOOLEAN_FIELDS_VALUE_NO => AmosAdmin::t('amosadmin', 'No'),
            UserProfile::BOOLEAN_FIELDS_VALUE_YES => AmosAdmin::t('amosadmin', 'Yes')
        ];
    }
    
    /**
     * Lists all UserProfile models.
     * @return mixed
     */
    public function actionIndex($layout = NULL)
    {
        Url::remember();
        if (!empty(Yii::$app->session['cwh-scope'])) {
            $this->setAvailableViews([
                'icon' => $this->iconView
            ]);
        } else {
            $this->setAvailableViews([
                'icon' => $this->iconView,
                'grid' => $this->gridView,
//                'list' => $this->listView
            ]);
        }
        $this->setDataProvider($this->getModelSearch()->search(Yii::$app->request->getQueryParams()));
        $this->setCreateNewBtnParams();
        $this->setListsViewParams();
        $this->setTitleAndBreadcrumbs(AmosAdmin::t('amosadmin', 'All users'));
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();
        return parent::actionIndex();
    }
    
    /**
     * Displays a single UserProfile model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Url::remember();
        $this->setUpLayout('main');
        $this->model = $this->findModel($id);

        return $this->render('view', [
            'model' => $this->model,
        ]);
    }
    
    /**
     * Creates a new UserProfile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->setUpLayout('form');
        
        /** @var UserProfile $profile */
        $profile = AmosAdmin::instance()->createModel('UserProfile');
        $profile->setScenario(UserProfile::SCENARIO_DYNAMIC);
        
        $defaultFacilitatorProfile = $profile->getDefaultFacilitator();
        if (!is_null($defaultFacilitatorProfile)) {
            $profile->facilitatore_id = $defaultFacilitatorProfile->id;
        }

        if (empty($profile->user_profile_role_id)){
            $profile->user_profile_role_id = 8;
        }

        if (empty($profile->user_profile_area_id)){
            $profile->user_profile_area_id = 13;
        }

        /** @var User $user */
        $user = AmosAdmin::instance()->createModel('User');
        
        // Salvo l'utente e subito dopo salvo il profilo agganciando l'utente
        if ($user->load(Yii::$app->request->post()) && $user->validate()) {
            if (!($profile->load(Yii::$app->request->post()) && $profile->validate())) {
                // QUALCOSA è andato storto! ERRORE...
                Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', 'Internal error. Impossible to link user to the relative profile.'));
                return $this->render('create', [
                    'model' => $profile,
                    'user' => $user,
                    'permissionSave' => 'USERPROFILE_CREATE'
                ]);
            }
            // Se mi trovo qua posso salvare entrambe le entità senza avere errore
            $user->save();
            $profile->user_id = $user->id;
            $profile->widgets_selected = 'a:2:{s:7:"primary";a:1:{i:0;a:6:{i:0;a:2:{s:4:"code";s:12:"USER_PROFILE";s:11:"module_name";s:5:"admin";}i:1;a:2:{s:4:"code";s:5:"USERS";s:11:"module_name";s:5:"admin";}i:2;a:2:{s:4:"code";s:11:"TAG_MANAGER";s:11:"module_name";s:3:"tag";}i:3;a:2:{s:4:"code";s:4:"ENTI";s:11:"module_name";s:4:"enti";}i:4;a:2:{s:4:"code";s:9:"ENTI_TIPO";s:11:"module_name";s:4:"enti";}i:5;a:2:{s:4:"code";s:4:"SEDI";s:11:"module_name";s:4:"enti";}}}s:5:"admin";a:1:{i:0;a:2:{i:0;a:2:{s:4:"code";s:12:"USER_PROFILE";s:11:"module_name";s:5:"admin";}i:1;a:2:{s:4:"code";s:5:"USERS";s:11:"module_name";s:5:"admin";}}}}';
            //If admin module bypasses workflow flag is set, user profile is already validated
            if(Yii::$app->getModule('admin')->bypassWorkflow){
               $profile->validato_almeno_una_volta = 1;
            }

            $savedProfile = $profile->save();
            //setting personal validation scope for contents if cwh module is enabled
            if ($savedProfile) {
                $cwhModule = Yii::$app->getModule('cwh');
                if (!empty($cwhModule)) {
                    $cwhModelsEnabled = $cwhModule->modelsEnabled;
                    foreach ($cwhModelsEnabled as $contentModel) {
                        $permissionCreateArray = [
                            'item_name' => $cwhModule->permissionPrefix . "_CREATE_" . $contentModel,
                            'user_id' => $profile->user_id,
                            'cwh_nodi_id' => 'user-' . $profile->user_id
                        ];
                        //add cwh permission to create content in 'Personal' scope
                        $cwhAssignCreate = new \lispa\amos\cwh\models\CwhAuthAssignment($permissionCreateArray);
                        $cwhAssignCreate->save(false);
                    }
                }
            }
            
            // Save email and sms notify frequency
            $notifyModule = Yii::$app->getModule('notify');
            if (!is_null($notifyModule)) {
                /** @var AmosNotify $notifyModule */
                $post = Yii::$app->request->post();
                $emailFrequency = 0;
                $smsFrequency = 0;
                $atLeastOne = false;
                if (isset($post[NotifyFrequencyWidget::emailFrequencySelectorName()])) {
                    $atLeastOne = true;
                    $emailFrequency = Yii::$app->request->post()[NotifyFrequencyWidget::emailFrequencySelectorName()];
                }
                if (isset($post[NotifyFrequencyWidget::smsFrequencySelectorName()])) {
                    $atLeastOne = true;
                    $smsFrequency = Yii::$app->request->post()[NotifyFrequencyWidget::smsFrequencySelectorName()];
                }
                if ($atLeastOne) {
                    $ok = $notifyModule->saveNotificationConf($user->id, $emailFrequency, $smsFrequency);
                    if (!$ok) {
                        Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', 'Error while saving email frequency'));
                        return $this->render('create', [
                            'model' => $profile,
                            'user' => $user,
                            'permissionSave' => 'USERPROFILE_CREATE',
                        ]);
                    }
                }
            }
            
            /** @var AmosAdmin $adminModule */
            $adminModule = \Yii::$app->getModule(AmosAdmin::getModuleName());
            Yii::$app->getAuthManager()->assign(Yii::$app->getAuthManager()->getRole($adminModule->defaultUserRole), $user->id);
            Yii::$app->getSession()->addFlash('success', AmosAdmin::t('amosadmin', 'Utente creato correttamente.'));
            //return $this->redirect(['view', 'id' => $this->model->id]);
            return $this->redirect(['update', 'id' => $profile->id]);
        } else {
            //Ripasso al form i dati inseriti anche se non corretti...
            $user->load(Yii::$app->request->post());
            $profile->load(Yii::$app->request->post());
            return $this->render('create', [
                'model' => $profile,
                'user' => $user,
                'permissionSave' => 'USERPROFILE_CREATE',
            ]);
        }
    }
    
    /**
     * Updates an existing UserProfile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $render = true, $tabActive = null)
    {
        Url::remember();
        $this->setUpLayout('form');
        $url = Yii::$app->urlManager->createUrl(['/admin/user-profile/update-profile', 'id' => $id]);
        
        if ($render) {
            $url = Yii::$app->urlManager->createUrl(['/admin/user-profile/update', 'id' => $id]);
        }
        
        // Finding the user profile model
        $this->model = $this->findModel($id);
        
        // Setting the dynamic scenario. It's compiled dinamically by the
        // configuration manager based on the module configurations.
        // Remove this row to restore the default functionalities.
        $this->model->setScenario(UserProfile::SCENARIO_DYNAMIC);
        
        if (Yii::$app->request->post()) {
            $ruoliUtente = (isset(\Yii::$app->request->post()[$this->getModelName()]['listaRuoli']) && is_array(\Yii::$app->request->post()[$this->getModelName()]['listaRuoli'])) ? \Yii::$app->request->post()[$this->getModelName()]['listaRuoli'] : [];
            $setRuoli = (isset(\Yii::$app->request->post()[$this->getModelName()]['listaRuoli'])) ? true : false;
//            $modelRuoli = AmosAdmin::instance()->createModel('Ruoli');
            
            /**
             * Keep track of old status
             */
            $currentStatus = $this->model->status;

            /**
             * Load post data
             */
            $this->model->load(Yii::$app->request->post());

            $this->model->user->load(Yii::$app->request->post());
            if ($this->model->validate() && $this->model->user->validate()) {

                if ($setRuoli) {
                    if (!empty($this->forzaListaRuoli)) {
                        // Se mi hanno forzato i ruoli, prendo buoni quelli passati
                        $this->model->setRuoli($this->forzaListaRuoli);
                        $this->forzaListaRuoli = null;
                    } else {
                        $this->model->setRuoli($ruoliUtente);
                    }
                }
                
                if ($this->model->status == UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED) {
                    $this->model->validato_almeno_una_volta = 1;
                }
                
                //If the previous status is validated return to draft
                if(!empty(\Yii::$app->request->post()['UserProfile']['isProfileModified'])) {
                    $isProfileModified = \Yii::$app->request->post()['UserProfile']['isProfileModified'];
                }
                if ($currentStatus == UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED
                    && !empty($isProfileModified) && $isProfileModified == 1) {
                    $this->model->status = UserProfile::USERPROFILE_WORKFLOW_STATUS_DRAFT;
                }
//                if ($currentStatus == UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED
//                    && $this->model->status == UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED) {
//                    $this->model->status = UserProfile::USERPROFILE_WORKFLOW_STATUS_DRAFT;
//                }
//
                if ($this->model->save() && $this->model->user->save()) {
//                    if ($setRuoli) {
//                        // Controllo che in ereditariatà mi abbiano settato i ruoli
//                        // con le logiche di ereditarierà
//                        if (!empty($this->forzaListaRuoli)) {
//                            // Se mi hanno forzato i ruoli, prendo buoni quelli passati
//                            $this->model->setRuoli($this->forzaListaRuoli);
//                            $this->forzaListaRuoli = null;
//                        } else {
//                            $this->model->setRuoli($ruoliUtente);
//                        }
//                    }
                    
                    // Save email and sms notify frequency
                    $notifyModule = Yii::$app->getModule('notify');
                    if (!is_null($notifyModule)) {
                        /** @var AmosNotify $notifyModule */
                        $post = Yii::$app->request->post();
                        $emailFrequency = 0;
                        $smsFrequency = 0;
                        $atLeastOne = false;
                        if (isset($post[NotifyFrequencyWidget::emailFrequencySelectorName()])) {
                            $atLeastOne = true;
                            $emailFrequency = Yii::$app->request->post()[NotifyFrequencyWidget::emailFrequencySelectorName()];
                        }
                        if (isset($post[NotifyFrequencyWidget::smsFrequencySelectorName()])) {
                            $atLeastOne = true;
                            $smsFrequency = Yii::$app->request->post()[NotifyFrequencyWidget::smsFrequencySelectorName()];
                        }
                        if ($atLeastOne) {
                            $ok = $notifyModule->saveNotificationConf($this->model->user->id, $emailFrequency, $smsFrequency);
                            if (!$ok) {
                                Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', 'Error while updating email frequency'));
                                if ($render) {
                                    $this->updateParamsRender = ArrayHelper::merge($this->updateParamsRender, [
                                        'user' => $this->model->user,
                                        'model' => $this->model,
                                        'tipologiautente' => $this->model->tipo_utente,
                                        'permissionSave' => 'USERPROFILE_UPDATE',
                                        'tabActive' => $tabActive,
                                    ]);
                                    return $this->render('update', $this->updateParamsRender);
                                } else {
                                    return $this->model;
                                }
                            }
                        }
                    }
                    
                    Yii::$app->getSession()->addFlash('success', AmosAdmin::t('amosadmin', 'Modifiche salvate con successo.'));
                    if ($render) {
                        return $this->redirect($url);
                    } else {
                        return $this->model;
                    }
                } else {
                    Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', 'Si &egrave; verificato un errore durante il salvataggio'));
                }
            } else {
                if (isset($this->model->user->getErrors()['email'])) {
                    Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', $this->model->user->getErrors()['email'][0]));
                } else {
                    Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', 'Modifiche non salvate. Verifica l\'inserimento dei campi, '));
                }
            }
            
        }
        
        if ($render) {
            $this->updateParamsRender = ArrayHelper::merge($this->updateParamsRender, [
                'user' => $this->model->user,
                'model' => $this->model,
                'tipologiautente' => $this->model->tipo_utente,
                'permissionSave' => 'USERPROFILE_UPDATE',
                'tabActive' => $tabActive,
            ]);
            return $this->render('update', $this->updateParamsRender);
        } else {
            return $this->model;
        }
    }
    
    /**
     * Deletes an existing UserProfile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->model = $this->findModel($id);
        //$user = AmosAdmin::instance()->createModel('User')->findOne(['id' => $this->model->user_id]);
        //da attivare con una transazione e i controlli su dove sono usati gli utenti (da altri 10 model) appena ne ho tempo
        $this->model->delete();
        
        if (!$this->model->getErrors()) {
//        $user->delete();
//        $connection = Yii::$app->db;
//        $transaction = $connection->beginTransaction();
//        try {
//            $transaction->commit();
            Yii::$app->getSession()->addFlash('success', AmosAdmin::t('amosadmin', 'Utente cancellato correttamente.'));
//        } catch (Exception $ex) {
//            $transaction->rollback();
//            Yii::$app->getSession()->addFlash('error', AmosAdmin::t('amosadmin', 'L\'utente non può essere cancellato'));
//            return $this->redirect(['index']);
//        }
        } else {
            Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', "Errori durante la cancellazione dell'utente."));
        }
        
        return $this->redirect(['index']);
    }
    
    /**
     * @param $path
     * @param $file
     * @param array $extensions
     * @return bool
     */
    protected function downloadFile($path, $file, $extensions = [])
    {
        if (is_file($path)) {
            $file_info = pathinfo($path);
            $extension = $file_info["extension"];
            if (is_array($extensions)) {
                foreach ($extensions as $e) {
                    if ($e === $extension) {
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename=Allegato_utente.' . $extension);
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($path));
                        ob_clean();
                        flush();
                        readfile($path);
                        
                        return true; //Yii::$app->response->sendFile($path);
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * @return array|null
     */
    public function getWhiteListRoles()
    {
        $arrayRuoli = null;
        $moduleWhite = $this->module->getWhiteListRoles();
        
        foreach ($moduleWhite as $rule) {
            $arrayRuoli[] = Yii::$app->authManager->getRole($rule);
        }
        return $arrayRuoli;
    }
    
    /**
     * @param int $lunghezza
     * @return string
     */
    protected function PasswordCasuale($lunghezza = 8)
    {
        $caratteri_disponibili = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!";
        
        $passwordcas = "";
        for ($i = 0; $i < $lunghezza; $i++) {
            $passwordcas = $passwordcas . substr($caratteri_disponibili, rand(0, strlen($caratteri_disponibili) - 1), 1);
        }
        return $passwordcas;
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
