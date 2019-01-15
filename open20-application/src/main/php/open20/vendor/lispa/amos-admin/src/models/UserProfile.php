<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\models
 * @category   CategoryName
 */

namespace lispa\amos\admin\models;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\admin\events\AdminWorkflowEvent;
use lispa\amos\admin\i18n\grammar\UserProfileGrammar;
use lispa\amos\admin\models\base\UserProfile as BaseUserProfile;
use lispa\amos\admin\utility\UserProfileUtility;
use lispa\amos\admin\widgets\icons\WidgetIconUserProfile;
use lispa\amos\attachments\behaviors\FileBehavior;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\helpers\T;
use lispa\amos\core\interfaces\ContentModelInterface;
use lispa\amos\core\interfaces\ViewModelInterface;
use lispa\amos\core\record\CachedActiveQuery;
use lispa\amos\core\user\User;
use lispa\amos\workflow\behaviors\WorkflowLogFunctionsBehavior;
use Exception;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class UserProfile
 *
 * This is the model class for table "user_profile".
 *
 * @property \lispa\amos\core\user\User $user
 * @property \lispa\amos\comuni\models\IstatComuni $residenzaComune
 * @property \lispa\amos\comuni\models\IstatProvince $residenzaProvincia
 *
 * @method \cornernote\workflow\manager\components\WorkflowDbSource getWorkflowSource()
 * @method \yii\db\ActiveQuery hasOneFile($attribute = 'file', $sort = 'id')
 * @method \yii\db\ActiveQuery hasMultipleFiles($attribute = 'file', $sort = 'id')
 *
 * @package lispa\amos\admin\models
 */
class UserProfile extends BaseUserProfile implements ContentModelInterface, ViewModelInterface
{
    // Event const
    const EVENT_AGGIORNA_RUOLO = 'aggiorna-ruolo';
    
    public $file;
    public $tipo_utente;
    public $sedi_facilitazione;
    public $ruolos;
    public $insegnamentis;
    public $ruolos2;
    public $insegnamentis2;
    public $insegnamentis3;
    public $listaRuoli;
    public $listaProgetti;
    public $isProfileModified;

    /**
     * @var \lispa\amos\attachments\models\File $userProfileImage
     */
    private $userProfileImage;
    
    protected static $scenariosNotToCheckTag = [
        self::SCENARIO_INTRODUCTION,
        self::SCENARIO_INTRODUCING_MYSELF,
        self::SCENARIO_ROLE_AND_AREA,
        self::SCENARIO_PARTNERSHIP,
        self::SCENARIO_SUMMARY
    ];

    /**
     * @var $validatori Validatori
     */
    public $validatori;


    /**
     * @inheritdoc
     */
    public function getViewUrl()
    {
        return "admin/user-profile/view";
    }
    
    /**
     * @inheritdoc
     */
    public function getFullViewUrl()
    {
        return Url::toRoute(["/" . $this->getViewUrl(), "id" => $this->id]);
    }
    
    /**
     * Getter for $this->userProfileImage;
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfileImage()
    {
        if (empty($this->userProfileImage)) {
            $this->userProfileImage = $this->hasOneFile('userProfileImage')->one();
        }
        return $this->userProfileImage;
    }
    
    /**
     * @param $image
     * @return mixed
     */
    public function setUserProfileImage($image)
    {
        return $this->userProfileImage = $image;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['userProfileImage'], 'file', 'extensions' => 'jpeg, jpg, png, gif'],
            ['codice_fiscale', 'string', 'length' => 16],
            ['codice_fiscale', 'checkCodiceFiscale'],
            [['avatar_id', 'listaRuoli', 'listaProgetti'], 'safe'],
            [['privacy', 'domicilio_provincia_id', 'domicilio_cap', 'domicilio_comune_id', 'created_by', 'updated_by', 'deleted_by'], 'default'],
            [['facebook', 'twitter', 'linkedin'], 'url'],
            [['ruolos', 'insegnamentis', 'ruolos2', 'insegnamentis2', 'insegnamentis3','isProfileModified'], 'safe']
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'userProfileImage' => AmosAdmin::t('amosadmin', 'Profile image'),
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return array(
            'nome',
            'cognome',
        );
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'workflow' =>
                [
                    'class' => SimpleWorkflowBehavior::className(),
                    'defaultWorkflowId' => self::USERPROFILE_WORKFLOW,
                    'propagateErrorsToModel' => true,
                ],
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ],
            'WorkflowLogFunctionsBehavior' => [
                'class' => WorkflowLogFunctionsBehavior::className(),
            ]
        ]);
        
        $cwhModule = Yii::$app->getModule('cwh');
        $tagModule = Yii::$app->getModule('tag');
        if (isset($cwhModule ) && isset($tagModule)) {
            $cwhTaggable = ['interestingTaggable' => [
                'class' => \lispa\amos\cwh\behaviors\TaggableInterestingBehavior::className(),
                // 'tagValuesAsArray' => false,
                // 'tagRelation' => 'tags',
                'tagValueAttribute' => 'id',
                'tagValuesSeparatorAttribute' => ',',
                'tagValueNameAttribute' => 'nome',
                //'tagFrequencyAttribute' => 'frequency',
            ]];
            
            $behaviors = ArrayHelper::merge($behaviors, $cwhTaggable);
        }

        return $behaviors;
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AGGIORNA_RUOLO, [$this, 'aggiornaRuolo']);
        
        if ($this->isNewRecord) {
            $this->status = $this->getWorkflowSource()->getWorkflow(self::USERPROFILE_WORKFLOW)->getInitialStatusId();
        }

        $adminModule = AmosAdmin::instance();
        if (!$adminModule->confManager->isVisibleBox('box_prevalent_partnership', ConfigurationManager::VIEW_TYPE_FORM) ||
            !$adminModule->confManager->isVisibleField('prevalent_partnership_id',ConfigurationManager::VIEW_TYPE_FORM))
        {
            if (isset($adminModule->params['orderParams']['user-profile']['fields'])) {
                $orderField = $adminModule->params['orderParams']['user-profile']['fields'];
                if(in_array('prevalentPartnership', $orderField)){
                    unset($adminModule->params['orderParams']['user-profile']['fields'][array_search ('prevalentPartnership', $orderField)]);

                }
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if(!\Yii::$app->user->can('ADMIN')) {
            if (!$this->checkOneTagPresent()) {
                return false;
            }
        }

        return parent::beforeValidate();
    }
    
    /**
     * Method to check if there is at least one tag selected.
     * @return bool
     */
    protected function checkOneTagPresent()
    {
        if (in_array($this->getScenario(), self::$scenariosNotToCheckTag) || $this->isNewRecord || empty(\Yii::$app->getModule('tag'))) {
            return true;
        }
        
        $formName = $this->formName();
        $post = \Yii::$app->getRequest()->post();
        if (isset($post[$formName])) {
            $tagValues = [];
            if (isset($post[$formName]['interestTagValues'])) {
                $tagValues = array_filter($post[$formName]['interestTagValues']);
            }
            $empty = true;
            foreach ($tagValues as $contentIndex => $content) {
                foreach ($content as $rootId => $tags) {
                    if (!empty($tags)) {
                        $empty = false;
                    }
                }
            }
            if ($empty) {
                $this->addError('interestTagValues', AmosAdmin::t('amosadmin', "It's necessary to indicate at least one tag."));
                return false;
            }
        }
        return true;
    }
    
    /**
     * This method return new instance of AdminWorkflowEvent
     * @return AdminWorkflowEvent
     */
    public function getAdminWorkflowEvent()
    {
        return new AdminWorkflowEvent();
    }
    

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        $IndirizzoCompleto = AmosAdmin::t('amosadmin', 'Senza indirizzo completo non si potr&agrave; usare la georeferenziazione');
        return ArrayHelper::merge(parent::attributeHints(), [
            'domicilio_indirizzo' => $IndirizzoCompleto,
            'domicilio_civico' => $IndirizzoCompleto,
            'domicilio_cap' => $IndirizzoCompleto,
            'domicilio_provincia_id' => $IndirizzoCompleto,
            'domicilio_comune_id' => $IndirizzoCompleto,
            'domicilio_localita' => $IndirizzoCompleto,
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!strlen($this->domicilio_provincia_id) || !$this->domicilio_provincia_id) {
            $this->domicilio_provincia_id = null;
        }
        if (!strlen($this->domicilio_comune_id) || !$this->domicilio_comune_id) {
            $this->domicilio_comune_id = null;
        }
        
        if (false) {
            $domicilioAttributes = [
                'domicilio_indirizzo',
                'domicilio_civico',
                'domicilio_cap',
                'domicilio_provincia_id',
                'domicilio_comune_id',
                'domicilio_localita',
            ];
            
            $coordAttributes = [
                'domicilio_coordinate'
            ];

//            if ($variazioniCoordinate = $this->getDirtyAttributes($coordAttributes)) {
//                Yii::$app->getSession()->addFlash('warning', AmosAdmin::t('amosadmin', 'Hai modificato le coordinate del tuo indirizzo!'));
//            }
            if ($variazioniIndirizzo = $this->getDirtyAttributes($domicilioAttributes)) {
                //Yii::$app->getSession()->addFlash('warning', AmosAdmin::t('amosadmin', 'Hai modificato l\'indirizzo!'));
                $Comune = $this->getDomicilioComune()->one();
                $Provincia = $this->getDomicilioProvincia()->one();
                $googleMapsKey = Yii::$app->params['google-maps']['key'];
                if ($Comune && $Provincia && $googleMapsKey) {
                    if ($this->domicilio_indirizzo) {
                        if ($this->domicilio_civico) {
                            $GeoCoderParams = urlencode("$this->domicilio_indirizzo, $this->domicilio_civico, $this->domicilio_cap $Comune->nome, Italy");
                        } else {
                            $GeoCoderParams = urlencode("$this->domicilio_indirizzo, $this->domicilio_cap $Comune->nome, Italy");
                        }
                    } else {
                        $GeoCoderParams = urlencode("$Comune->nome $Provincia->sigla, Italy");
                    }
                    
                    //$UrlGeocoder = "http://utility.lispa.it/geo/geo/getcoords?indirizzo=$GeoCoderParams";
                    $UrlGeocoder = "https://maps.googleapis.com/maps/api/geocode/json?address=$GeoCoderParams&key=$googleMapsKey";
                    $ResulGeocoding = Json::decode(file_get_contents($UrlGeocoder));
                    if ($ResulGeocoding && isset($ResulGeocoding['status'])) {
                        
                        if ($ResulGeocoding['status'] == 'OK') {
                            
                            if (isset($ResulGeocoding['results']) && isset($ResulGeocoding['results'][0])) {
                                $Indirizzo = $ResulGeocoding['results'][0];
                                if (isset($Indirizzo['geometry'])) {
                                    
                                    if (isset($Indirizzo['geometry']['location'])) {
                                        $Location = $Indirizzo['geometry']['location'];
                                        
                                        if (isset($Location['lat'])) {
                                            $this->domicilio_lat = $Location['lat'];
                                        }
                                        if (isset($Location['lng'])) {
                                            $this->domicilio_lon = $Location['lng'];
                                        }
                                    }
                                    $this->domicilio_coordinate = $this->domicilio_lat . "," . $this->domicilio_lon;
                                    //Yii::$app->getSession()->addFlash('warning', AmosAdmin::t('amosadmin', 'Le coordinate sono state calcolate in base all\'indirizzo fornito, puoi spostare il segnaposto per indicare la posizione precisa.'));
                                }
                            }
                        } elseif ($ResulGeocoding['status'] == 'ZERO_RESULTS') {
                            Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', 'Il tuo indirizzo non &egrave stato trovato. Verifica che sia completo, indica esattamente il nome della via, civico (nel campo apposito) e cap.'));
                        }
                    } else {
                        Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', 'Si &egrave; verificato un errore durante il reperimento delle coordinate del tuo indirizzo, verifica la correttezza dei dati forniti.'));
                    }
                }
            }
        }
        
        if ($this->status == self::USERPROFILE_WORKFLOW_STATUS_VALIDATED) {
            $this->validato_almeno_una_volta = 1;
        }
        
        return parent::beforeSave($insert);
    }

//    public function getTuttiRuoli()
//    {
//        return AmosAdmin::instance()->createModel('Ruoli')->find()->all()->distinct();
//    }
    
    public function getContatti($id = NULL)
    {
        if (!$id) {
            $id = $this->id;
        }
        $RichiesteInviate = (new Query())->select('id_richiedente, id_destinatario, stato_id, data_richiesta, data_accettazione, descrizione, nome, cognome, avatar_id, Prof.id as id, id_contatto')
            ->from('user_profile_contatti_mm     Inv')
            ->orWhere(['Inv.id_richiedente' => $id])
            ->andWhere(['Inv.deleted_by' => NULL])
            ->innerJoin('user_profile Prof', 'Prof.id = Inv.id_destinatario')
            ->innerJoin('user_profile_contatti_stati Stat', 'Inv.stato_id = Stat.id');
        
        $RichiesteRicevute = (new Query())->select('id_richiedente, id_destinatario, stato_id, data_richiesta, data_accettazione, descrizione, nome, cognome, avatar_id, Profil.id as id, id_contatto')
            ->from('user_profile_contatti_mm     Ric')
            ->orWhere(['Ric.id_destinatario' => $id])
            ->andWhere(['Ric.deleted_by' => NULL])
            ->innerJoin('user_profile Profil', 'Profil.id = Ric.id_richiedente')
            ->innerJoin('user_profile_contatti_stati Statt', 'Ric.stato_id = Statt.id');
        
        return $RichiesteInviate->union($RichiesteRicevute);
    }
    
    /**
     * Restituisce il nome e il cognome dei contatti dell'utente
     * @param integer $id L'id dell'utente dei quali si vuole conoscere i contatti, se non inserito è dell'utente attuale
     * @return ActiveRecord il risultato della query
     */
    public function getContattiNomeCognome($id = NULL)
    {
        if (!$id) {
            $id = $this->id;
        }
        $RichiesteInviate = (new Query())->select(['nome_completo' => 'CONCAT(nome, " ", cognome)', 'id' => 'Prof.id'])
            ->from('user_profile_contatti_mm     Inv')
            ->orWhere(['Inv.id_richiedente' => $id])
            ->andWhere(['Inv.deleted_by' => NULL])
            ->innerJoin('user_profile Prof', 'Prof.id = Inv.id_destinatario')
            ->innerJoin('user_profile_contatti_stati Stat', 'Inv.stato_id = Stat.id');
        
        $RichiesteRicevute = (new Query())->select(['nome_completo' => 'CONCAT(nome, " ", cognome)', 'id' => 'Profil.id'])
            ->from('user_profile_contatti_mm     Ric')
            ->orWhere(['Ric.id_destinatario' => $id])
            ->andWhere(['Ric.deleted_by' => NULL])
            ->innerJoin('user_profile Profil', 'Profil.id = Ric.id_richiedente')
            ->innerJoin('user_profile_contatti_stati Statt', 'Ric.stato_id = Statt.id');
        
        return $RichiesteInviate->union($RichiesteRicevute);
    }
    
    public function getWidgetsSelectedToArray()
    {
        return unserialize($this->widgets_selected);
    }
    
    public function setWidgetsSelectedFromArray($widgets = [])
    {
        $this->widgets_selected = serialize($widgets);
    }
    
    public function getIndirizzoCompleto()
    {
        
        $indirizzoCompleto = '';
        $Comune = $this->getDomicilioComune()->one();
        $Provincia = $this->getDomicilioProvincia()->one();
        if ($Comune && $Provincia) {
            $indirizzoCompleto = "$this->domicilio_indirizzo $this->domicilio_civico $Comune->nome ($Provincia->sigla)";
        }
        return $indirizzoCompleto;
    }
    
    public function checkAnomimo($attribute)
    {
        $campiVisualizzabili = [];
        if ($privacyCheck = $this->getUserProfilePrivacy()->one()) {
            $campiVisualizzabili = explode(',', $privacyCheck->campi_visualizzati);
        }
        $val = 'n.d';
        $fnGetterAttribute = 'get' . ucfirst($attribute);
        //se il campo è nell'elenco dei visualizzabili
        if ($attribute == 'email') {
            if (in_array($attribute, $campiVisualizzabili)) {
                $val = $this->getUser()->one()->email;
            }
            return $val;
        } else if ($attribute == 'codice_fiscale') {
            if (in_array($attribute, $campiVisualizzabili)) {
                $val = $this->codice_fiscale;
                $val = strtoupper($val);
            }
            return $val;
        } elseif (in_array($attribute, $campiVisualizzabili)) {
            if (method_exists($this, $fnGetterAttribute)) {
                $val = $this->$fnGetterAttribute();
            } else {
                $val = $this->$attribute;
            }
        }
        return $val;
    }
    
    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('User')->className(), ['id' => 'user_id']);
    }
    
    public function getCompletamentoProfilo()
    {
        $campiConsigliati = $this->getCampiConsigliati();
        $campiConsigliati++;
        $compilati = -1;
        foreach ($campiConsigliati as $campo) {
            if (isset($this->$campo) && strlen($this->$campo)) {
                $compilati++;
            } else {
            
            }
        }
        $percentuale = 0;
        $id = Yii::$app->getUser()->getId();
        $email = AmosAdmin::instance()->createModel('User')->findOne(['id' => $id])->getAttribute('email');
        if ($email && strlen($email)) {
            $compilati++;
        }
        if ($compilati > -1) {
            $percentuale = $compilati * 100 / (count($campiConsigliati));
        }
        return round($percentuale);
    }
    
    /**
     * 
     * @return type
     */
    public function getCampiConsigliati()
    {
        return [
            'nome',
            'cognome',
            'sesso',
            'codice_fiscale',
            //'nascita_data',
            'privacy',
            'avatar_id',
            'user_profile_privacy_id',
            'domicilio_indirizzo',
            'domicilio_civico',
            'domicilio_cap',
            'domicilio_provincia_id',
            'domicilio_comune_id',
            'domicilio_coordinate',
            'domicilio_lat',
            'domicilio_lon',
        ];
    }
    
    /**
     * 
     * @param type $dimension
     * @return type
     */
    public function getAvatar($dimension = 'original')
    {
        $image = null;
        if($this->sesso == 'Maschio'){
            $image = file_get_contents('@backend/web/img/defaultProfiloM.png');
        } elseif($this->sesso == 'Femmina') {
            $image = file_get_contents('@backend/web/img/defaultProfiloF.png');
        } else {
            $image = file_get_contents('@backend/web/img/defaultProfilo.png');
        }

        if (!empty($this->getUserProfileImage())) {
            $image = $this->userProfileImage;
        }
        
        return $image;
    }
    
    
    /**
     * 
     * @param type $dimension
     * @return type
     */
    public function getAvatarUrl($dimension = 'original')
    {
        if($this->sesso == 'Maschio'){
            $url = \Yii::$app->getUrlManager()->createAbsoluteUrl(Url::to('/img/defaultProfiloM.png'));
        } elseif($this->sesso == 'Femmina') {
            $url = \Yii::$app->getUrlManager()->createAbsoluteUrl(Url::to('/img/defaultProfiloF.png'));
        } else {
            $url = \Yii::$app->getUrlManager()->createAbsoluteUrl(Url::to('/img/defaultProfilo.png'));
        }

        if (!empty($this->getUserProfileImage())) {
            $url = $this->userProfileImage->getUrl($dimension,false, true);
        }
        
        return $url;
    }
    
    /**
     * 
     * @param type $dimension
     * @return type
     */
    public function getAvatarWebUrl($dimension = 'original')
    {
        if($this->sesso == 'Maschio'){
            $url = \Yii::$app->getUrlManager()->createAbsoluteUrl(Url::to('/img/defaultProfiloM.png'));
        } elseif($this->sesso == 'Femmina') {
            $url = \Yii::$app->getUrlManager()->createAbsoluteUrl(Url::to('/img/defaultProfiloF.png'));
        } else {
            $url = \Yii::$app->getUrlManager()->createAbsoluteUrl(Url::to('/img/defaultProfilo.png'));
        }

        if (!empty($this->getUserProfileImage())) {
            $url = $this->userProfileImage->getWebUrl($dimension, true, true);
        }
        
        return $url;
    }
    
    /**
     * 
     * @param type $url
     * @return boolean
     */
    protected function image_exists($url)
    {
        try {
            if (getimagesize(Yii::$app->getBasePath() . '/web' . $url)) {
                return TRUE;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * @param string $dimension
     * @param bool $email
     * @return string
     * @deprecated since version 2.0
     */
    public function getLogoImg($dimension = 'original', $email = false)
    {
        return '/img/logo.png';
        if ($this->getCartaAttiva()->one()) {
            return '/img/logo.png';
        }
        if ($email) {
            return '/img/logo.png';
        } else {
            return '/img/img_default.jpg';
        }
    }
    
    /**
     * @param string $dimension
     * @param array $options
     * @return string
     * @deprecated since version 2.0
     */
    public function getAvatarImage($dimension = 'small', $options = [])
    {
        // TODO verificare a cosa devono corrispondere e se questo metodo serve ancora.
        $width = null;
        $height = null;
        return Html::img("/img/defaultProfilo.png", ['width' => $width, 'height' => $height, 'class' => 'media-object avatar']);
    }
    
    public function setAccettazionePrivacy()
    {
        $this->detachBehavior('many2many');
        $this->privacy = 1;
        $this->save(false);
    }
    
    public function beforeDelete()
    {
        $userId = Yii::$app->getUser()->getId();
        if ($userId == $this->user_id) {
            Yii::$app->getSession()->addFlash('danger', T::tDyn('Impossibile cancellare se stesso.'));
            return false;
        }
        
        //L'eliminazione del profilo deve provocare l'eliminazione dell'utente associato
        $utente = AmosAdmin::instance()->createModel('User')->findOne($this->user_id);
        $utente->delete();
        return parent::beforeDelete();
    }
    
    public function getVerificaUtenteProprio($idUtente)
    {
        $UtenteConnesso = Yii::$app->getUser()->getId();
        return $this->find()->andWhere(['id' => $idUtente])->andWhere(['created_by' => $UtenteConnesso]);
    }
    
    public function checkCodiceFiscale($attribute, $params)
    {
        $codiceFiscale = $this->$attribute;
        if (!$codiceFiscale) {
            $isValid = true;
        } // se non può essere null se ne deve occupare qualcun altro
        if (strlen($codiceFiscale) != 16) {
            $isValid = false;
        } else {
            $codiceFiscale = strtoupper($codiceFiscale);
            if (!preg_match("/^[A-Z0-9]+$/", $codiceFiscale)) {
                $isValid = false;
            }
            $s = 0;
            for ($i = 1; $i <= 13; $i += 2) {
                $c = $codiceFiscale[$i];
                if ('0' <= $c && $c <= '9')
                    $s += ord($c) - ord('0');
                else
                    $s += ord($c) - ord('A');
            }
            for ($i = 0; $i <= 14; $i += 2) {
                $c = $codiceFiscale[$i];
                switch ($c) {
                    case '0':
                        $s += 1;
                        break;
                    case '1':
                        $s += 0;
                        break;
                    case '2':
                        $s += 5;
                        break;
                    case '3':
                        $s += 7;
                        break;
                    case '4':
                        $s += 9;
                        break;
                    case '5':
                        $s += 13;
                        break;
                    case '6':
                        $s += 15;
                        break;
                    case '7':
                        $s += 17;
                        break;
                    case '8':
                        $s += 19;
                        break;
                    case '9':
                        $s += 21;
                        break;
                    case 'A':
                        $s += 1;
                        break;
                    case 'B':
                        $s += 0;
                        break;
                    case 'C':
                        $s += 5;
                        break;
                    case 'D':
                        $s += 7;
                        break;
                    case 'E':
                        $s += 9;
                        break;
                    case 'F':
                        $s += 13;
                        break;
                    case 'G':
                        $s += 15;
                        break;
                    case 'H':
                        $s += 17;
                        break;
                    case 'I':
                        $s += 19;
                        break;
                    case 'J':
                        $s += 21;
                        break;
                    case 'K':
                        $s += 2;
                        break;
                    case 'L':
                        $s += 4;
                        break;
                    case 'M':
                        $s += 18;
                        break;
                    case 'N':
                        $s += 20;
                        break;
                    case 'O':
                        $s += 11;
                        break;
                    case 'P':
                        $s += 3;
                        break;
                    case 'Q':
                        $s += 6;
                        break;
                    case 'R':
                        $s += 8;
                        break;
                    case 'S':
                        $s += 12;
                        break;
                    case 'T':
                        $s += 14;
                        break;
                    case 'U':
                        $s += 16;
                        break;
                    case 'V':
                        $s += 10;
                        break;
                    case 'W':
                        $s += 22;
                        break;
                    case 'X':
                        $s += 25;
                        break;
                    case 'Y':
                        $s += 24;
                        break;
                    case 'Z':
                        $s += 23;
                        break;
                }
            }
            if (isset($codiceFiscale[15])) {
                
                if (chr($s % 26 + ord('A')) != $codiceFiscale[15]) {
                    $isValid = false;
                } else {
                    $isValid = true;
                }
            }
        }
        if (!$isValid) {
            $this->addError($attribute, AmosAdmin::t('amosadmin', 'Il codice fiscale non è in un formato consentito'));
        }
    }
    
    /**
     * Funzione che restituisce tutti gli utenti facilitatori presenti nel sistema
     * @return ActiveQuery
     * @deprecated since version 2.0
     */
    public function getAllUtentiFacilitatori()
    {
        $NomeRuolo = 'FACILITATORE';
        return AmosAdmin::instance()->createModel('UserProfile')->find()->leftJoin('auth_assignment', 'user_profile.user_id = auth_assignment.user_id')->andWhere(['auth_assignment.item_name' => $NomeRuolo]);
    }
    
    /**
     * Method that returns all facilitators users present in the system.
     * @return UserProfile[]
     */
    public function getAllFacilitatorUserProfiles()
    {
        $facilitatorUserIds = UserProfileUtility::getAllFacilitatorUserIds();
        return AmosAdmin::instance()->createModel('UserProfile')->find()->andWhere(['user_id' => $facilitatorUserIds])->all();
    }
    
    /**
     * Funzione che verifica l'esistenza o meno del codice fiscale all'interno del sistema
     * @param string $cf Codice fiscale da verificare
     * @return ActiveQuery
     */
    public function getUtenteByCodiceFiscale($cf)
    {
        $cf = strtoupper($cf);
        return $this->find()->andWhere(['codice_fiscale' => $cf]);
    }
    
    /**
     * Funzione che verifica la correttezza del codice fiscale
     * @param string $cf Codice fiscale da verificare
     * @return boolean TRUE|false se il codice è corretto restituisce True altrimenti False
     */
    public function verificaCodiceFiscale($cf)
    {
        $codiceFiscale = $cf;
        if (!$codiceFiscale) {
            $isValid = true;
        } // se non può essere null se ne deve occupare qualcun altro
        if (strlen($codiceFiscale) != 16) {
            $isValid = false;
        } else {
            $codiceFiscale = strtoupper($codiceFiscale);
            if (!ereg("^[A-Z0-9]+$", $codiceFiscale)) {
                $isValid = false;
            }
            $s = 0;
            for ($i = 1; $i <= 13; $i += 2) {
                $c = $codiceFiscale[$i];
                if ('0' <= $c && $c <= '9')
                    $s += ord($c) - ord('0');
                else
                    $s += ord($c) - ord('A');
            }
            for ($i = 0; $i <= 14; $i += 2) {
                $c = $codiceFiscale[$i];
                switch ($c) {
                    case '0':
                        $s += 1;
                        break;
                    case '1':
                        $s += 0;
                        break;
                    case '2':
                        $s += 5;
                        break;
                    case '3':
                        $s += 7;
                        break;
                    case '4':
                        $s += 9;
                        break;
                    case '5':
                        $s += 13;
                        break;
                    case '6':
                        $s += 15;
                        break;
                    case '7':
                        $s += 17;
                        break;
                    case '8':
                        $s += 19;
                        break;
                    case '9':
                        $s += 21;
                        break;
                    case 'A':
                        $s += 1;
                        break;
                    case 'B':
                        $s += 0;
                        break;
                    case 'C':
                        $s += 5;
                        break;
                    case 'D':
                        $s += 7;
                        break;
                    case 'E':
                        $s += 9;
                        break;
                    case 'F':
                        $s += 13;
                        break;
                    case 'G':
                        $s += 15;
                        break;
                    case 'H':
                        $s += 17;
                        break;
                    case 'I':
                        $s += 19;
                        break;
                    case 'J':
                        $s += 21;
                        break;
                    case 'K':
                        $s += 2;
                        break;
                    case 'L':
                        $s += 4;
                        break;
                    case 'M':
                        $s += 18;
                        break;
                    case 'N':
                        $s += 20;
                        break;
                    case 'O':
                        $s += 11;
                        break;
                    case 'P':
                        $s += 3;
                        break;
                    case 'Q':
                        $s += 6;
                        break;
                    case 'R':
                        $s += 8;
                        break;
                    case 'S':
                        $s += 12;
                        break;
                    case 'T':
                        $s += 14;
                        break;
                    case 'U':
                        $s += 16;
                        break;
                    case 'V':
                        $s += 10;
                        break;
                    case 'W':
                        $s += 22;
                        break;
                    case 'X':
                        $s += 25;
                        break;
                    case 'Y':
                        $s += 24;
                        break;
                    case 'Z':
                        $s += 23;
                        break;
                }
            }
            if (isset($codiceFiscale[15])) {
                
                if (chr($s % 26 + ord('A')) != $codiceFiscale[15]) {
                    $isValid = false;
                } else {
                    $isValid = true;
                }
            }
        }
        if (!$isValid) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Restituisce il nome e cognome degli utenti
     */
    public function getNomeCognome()
    {
        return $this->nome . " " . $this->cognome;
    }
    
    /**
     * get the the user name format Surname + name
     */
    public function getSurnameName()
    {
        return $this->cognome . " " . $this->nome;
    }
    
    /**
     * @return ActiveQuery
     */
    public function getRuolos()
    {
        return $this->hasMany(AmosAdmin::instance()->createModel('Ruoli')->className(), ['id' => 'ruoli_id'])->viaTable('user_profile_ruoli_mm', ['user_profile_id' => 'id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getResidenzaComune()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatComuni')->className(), ['id' => 'comune_residenza_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getResidenzaProvincia()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatProvince')->className(), ['id' => 'provincia_residenza_id']);
    }

//    /**
//     * Restituisce tutti ruoli dell'utente
//     */
//    public function getRuoli()
//    {
//        $tuttiRuoli = $this->getTuttiRuoli();
//        $RuoliDisponibili = [];
//        foreach ($tuttiRuoli->select('ruolo')->all() as $Ruoli) {
//            $RuoliDisponibili[] = $Ruoli['ruolo'];
//        }
//        $ruoliAssegnati = [];
//        $ruoliUtente = (new \yii\db\Query())
//            ->select('item_name')
//            ->from('auth_assignment')
//            ->andWhere(['user_id' => $this->user_id])
//            ->andWhere(['IN', 'item_name', $RuoliDisponibili]);
//
//
//        foreach ($ruoliUtente->all() as $RuoloUt) {
//            $ruoliAssegnati[] = $RuoloUt['item_name'];
//        }
//        return $ruoliAssegnati;
//    }
    
    /**
     * Salva i ruoili
     */
    public function setRuoli($ruoli)
    {
        $ruoliAttuali = \Yii::$app->authManager->getRolesByUser($this->user_id);
        $arrRuoli = [];
        foreach ($ruoliAttuali as $ruoliAtt) {
            $arrRuoli[] = $ruoliAtt->name;
        }
        if ((count($ruoli) > 0) && ($ruoli != '')) {
            foreach ($ruoli as $Ruolo) {
                if (!in_array($Ruolo, $arrRuoli)) {
                    if (!(isset($ruoliAttuali[$Ruolo]))) {
                        $oggettoRuolo = \Yii::$app->authManager->getRole($Ruolo);
                        \Yii::$app->authManager->assign($oggettoRuolo, $this->user_id);
                    }
                }
            }
            if (count($arrRuoli) > 0) {
                foreach ($arrRuoli as $Role) {
                    if (!($Role == 'ADMIN' && $this->user_id == 1)) {
                        if (!in_array($Role, $ruoli)) {
                            $oggettoRuolo = \Yii::$app->authManager->getRole($Role);
                            \Yii::$app->authManager->revoke($oggettoRuolo, $this->user_id);
                        }
                    }
                }
            }
        } else {
            if (count($arrRuoli) > 0) {
                foreach ($arrRuoli as $Role) {
                    if (!($Role == 'ADMIN' && $this->user_id == 1)) {
                        if (!in_array($Role, $ruoli)) {
                            $oggettoRuolo = \Yii::$app->authManager->getRole($Role);
                            \Yii::$app->authManager->revoke($oggettoRuolo, $this->user_id);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Restituisce il percorso del marker, da personalizzare a piacimento
     * @return string Il percorso del marker che sarà utilizzato nella mappa
     */
    public function getIconaMarker()
    {
        return null;
    }
    
    /**
     *
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->loadListaRuoli();
    }
    
    /**
     *
     */
    private function loadListaRuoli()
    {
        $this->listaRuoli = ArrayHelper::map(Yii::$app->authManager->getRolesByUser($this->user_id), 'name', 'name');
    }
    
    /**
     * Metodo recuperare tutti gli stati da visualizzare nel widget di scelta degli stati. RItorna un array chiave => valore
     * con chiave l'id dello stato e valore lo stato da visualizzare.
     *
     * @return  array
     */
    public function getUserProfileStatuses()
    {
        $workFlowStatus = [];   // Stati del workflow
        
        if ($this->hasWorkflowStatus()) {  // Ho già lo stato. Model già salvato una volta.
            $allStatus = $this->getWorkflow()->getAllStatuses();   // Tutti gli stati del workflow
            $modelStatus = $this->getWorkflowStatus()->getId();    // Stato del model
            $actualStatusObj = $allStatus[$modelStatus];
            $workFlowStatus[$actualStatusObj->getId()] = $actualStatusObj->getLabel();    // Aggiungo lo stato iniziale a quelli da visualizzare.
            // Composizione di tutti gli altri stati possibili a partire dall'attuale, ovvero le transazioni possibili.
            $transitions = $this->getWorkflowSource()->getTransitions($modelStatus);
            foreach ($transitions as $transition) {
                $workFlowStatus[$transition->getEndStatus()->getId()] = $transition->getEndStatus()->getLabel();
            }
        } else {                                // Non ho lo stato. Model mai salvato. Faccio vedere solo quello iniziale.
            $contentDefaultWorkflow = $this->getWorkflowSource()->getWorkflow(UserProfile::USERPROFILE_WORKFLOW);
            $allStatus = $contentDefaultWorkflow->getAllStatuses();     // Tutti gli stati del workflow
            $initialStatusObj = $allStatus[$contentDefaultWorkflow->getInitialStatusId()];
            $workFlowStatus[$initialStatusObj->getId()] = $initialStatusObj->getLabel();
        }
        
        return $workFlowStatus;
    }
    
    /**
     * This method activate a user profile with activation of the relative user.
     * @return bool
     */
    public function activateUserProfile()
    {
        $this->attivo = self::STATUS_ACTIVE;
        $ok = $this->save(false);
        if ($ok) {
            $this->user->status = User::STATUS_ACTIVE;
            $ok = $this->user->save(false);
        }
        return $ok;
    }
    
    /**
     * This method deactivate a user profile with deactivation of the relative user.
     * @return bool
     */
    public function deactivateUserProfile()
    {
        $this->attivo = self::STATUS_DEACTIVATED;
        $ok = $this->save(false);
        if ($ok) {
            $this->user->status = User::STATUS_DELETED;
            $ok = $this->user->save(false);
        }
        return $ok;
    }
    
    /**
     * This method check if a user profile is active.
     * @return bool
     */
    public function isActive()
    {
        return (($this->attivo == self::STATUS_ACTIVE) && ($this->user->status == User::STATUS_ACTIVE));
    }
    
    /**
     * This method check if a user profile is deactivated.
     * @return bool
     */
    public function isDeactivated()
    {
        return (($this->attivo == self::STATUS_DEACTIVATED) && ($this->user->status == User::STATUS_DELETED));
    }
    
    /**
     * This method return the facilitator profile of this user profile. If not present,
     * it return the default facilitator profile. If not present it return null.
     * @return \lispa\amos\admin\models\UserProfile|null
     */
    public function getFacilitatorOrDefFacilitator()
    {
        // If present return the facilitator profile immediately...
        if (!is_null($this->facilitatore)) {
            return $this->facilitatore;
        }
        // ...otherwise find the default facilitator if present.
        return $this->getDefaultFacilitator();
    }
    
    /**
     * This method returns the default facilitator profile. If not present it returns null.
     * @return \lispa\amos\admin\models\UserProfile|null
     */
    public function getDefaultFacilitator()
    {
        // Find all facilitators user profile ids
        $allFacilitators = $this->getAllFacilitatorUserProfiles();
        $facilitatorUserProfileIds = [];
        foreach ($allFacilitators as $facilitatorProfile) {
            /** @var UserProfile $facilitatorProfile */
            $facilitatorUserProfileIds[] = $facilitatorProfile->id;
        }
        /** @var UserProfile $userProfile */
        $userProfile = AmosAdmin::instance()->createModel('UserProfile');
        // Find the default facilitator user profile.
        $facilitatorUserProfile = $userProfile->findOne(['default_facilitatore' => 1, 'id' => $facilitatorUserProfileIds]);
        return $facilitatorUserProfile;
    }
    
    /**
     * This method return true if the user passed by param is a facilitator.
     * If the user id is not passed, it uses the user id of the model.
     * If not present it return false.
     * @param int $userId
     * @return bool
     */
    public function isFacilitator($userId = 0)
    {
        if (!$userId) {
            if (!$this->user_id) {
                return false;
            }
            $userId = $this->user_id;
        }
        $facilitatorUserIds = UserProfileUtility::getAllFacilitatorUserIds();
        return (in_array($userId, $facilitatorUserIds));
    }
    
    /**
     * @return string The name that correspond to 'to validate' status for the content model
     */
    public function getToValidateStatus()
    {
        return self::USERPROFILE_WORKFLOW_STATUS_TOVALIDATE;
    }
    
    /**
     * @return string The name that correspond to 'published' status for the content model
     */
    public function getValidatedStatus()
    {
        return self::USERPROFILE_WORKFLOW_STATUS_VALIDATED;
    }
    
    /**
     * @return string The name that correspond to 'draft' status for the content model
     */
    public function getDraftStatus()
    {
        return self::USERPROFILE_WORKFLOW_STATUS_DRAFT;
    }
    
    /**
     * @return string The name of model validator role
     */
    public function getValidatorRole()
    {
        return 'ADMIN';
    }
    
    /**
     * @return mixed
     */
    public function getGrammar()
    {
        return new UserProfileGrammar();
    }
    
    
    /**
     * Get the UserProfiles to invite or with pending invitation (for logged user)
     *
     * @return @var ActiveQuery|CachedActiveQuery $query
     */
    public function getUserNetworkAssociationQuery()
    {
        $loggedUserId = Yii::$app->user->id;
        $adminModule = AmosAdmin::getInstance();
        
        $queryUserContactInvited = UserContact::find()
            ->andWhere(['contact_id' => $loggedUserId])
            ->andWhere(['<>', 'status', UserContact::STATUS_INVITED]);
        
        $queryUserContactInviting = UserContact::find()
            ->andWhere(['user_id' => $loggedUserId])
            ->andWhere(['<>', 'status', UserContact::STATUS_INVITED]);
        
        $contactsInvited = $queryUserContactInvited->select('user_id')->column();
        $contactsInviting = $queryUserContactInviting->select('contact_id')->column();
        
        $arrayIdActiveContacts = \yii\helpers\ArrayHelper::merge($contactsInvited, $contactsInviting);
        
        $queryContacts = UserProfile::find()
            ->andWhere(['not in', 'user_id', $arrayIdActiveContacts])
            ->andWhere(['<>', 'user_id', $loggedUserId])
            ->andWhere(['user_profile.validato_almeno_una_volta' => 1])
            ->andWhere(['user_profile.attivo' => 1]);
        
        if ($adminModule->cached) {
            $query = CachedActiveQuery::instance($queryContacts);
            $query->cache($adminModule->cacheDuration);
        } else {
            $query = $queryContacts;
        }
        
        return $query;
    }
    
    /**
     * @return string The model title field value
     */
    public function getTitle()
    {
        return $this->getNomeCognome();
    }
    
    /**
     * @return string The model description field value
     */
    public function getDescription($truncate)
    {
        return $this->getNomeCognome();
    }
    
    /**
     * @return array The columns ti show as default in GridViewWidget
     */
    public function getGridViewColumns()
    {
        return [];
    }
    
    /**
     * @return string|null date begin of publication
     */
    public function getPublicatedFrom()
    {
        return null;
    }

    /**
     * @return string|null date end of publication
     */
    public function getPublicatedAt()
    {
        return null;
    }
    
    /**
     * @return \yii\db\ActiveQuery category of content
     */
    public function getCategory()
    {
        return "";
    }
    
    /**
     * @return string The classname of the generic dashboard widget to access the plugin
     */
    public function getPluginWidgetClassname()
    {
        return WidgetIconUserProfile::className();
    }

    /**
     * method return user ids of record validators
     * @return array
     */
    public function getValidatorUsersId()
    {
        $users = [];

        try {
            if(!empty( $this->facilitatore_id)) {
                $users[] = $this->facilitatore_id;
            }
            if (empty($users) && $this instanceof WorkflowModelInterface) {
                $validatorRole = $this->getValidatorRole();
                $authManager = \Yii::$app->authManager;
                $users = $authManager->getUserIdsByRole($validatorRole);
            }

        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $users;
    }

    /**
     *
     */
    public function getNotifiedUserId(){
        return $this->user_id;
    }

    /**
     * @return array list of statuses that for cwh is validated
     */
    public function getCwhValidationStatuses()
    {
        return [$this->getValidatedStatus()];
    }
}
