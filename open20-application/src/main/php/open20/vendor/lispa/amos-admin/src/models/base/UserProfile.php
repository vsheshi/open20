<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\models\base
 * @category   CategoryName
 */

namespace lispa\amos\admin\models\base;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\core\helpers\Html;
use lispa\amos\notificationmanager\record\NotifyAuditRecord;
use yii\helpers\ArrayHelper;
use lispa\amos\admin\interfaces\OrganizationsModuleInterface;

/**
 * This is the base-model class for table "user_profile".
 *
 * @property integer $id
 * @property string $nome
 * @property string $cognome
 * @property string $codice_fiscale
 * @property string $sesso
 * @property string $presentazione_breve
 * @property string $presentazione_personale
 * @property string $nascita_data
 * @property integer $privacy
 * @property string $indirizzo_residenza
 * @property string $cap_residenza
 * @property string $numero_civico_residenza
 * @property string $domicilio_indirizzo
 * @property string $domicilio_civico
 * @property integer $domicilio_cap
 * @property string $domicilio_localita
 * @property string $domicilio_lat
 * @property string $domicilio_lon
 * @property string $widgets_selected
 * @property string $first_access_wizard_steps_accessed
 * @property string $nazionalita
 * @property string $email_pec
 * @property string $altri_dati_contatto
 * @property string $telefono
 * @property string $cellulare
 * @property string $fax
 * @property integer $attivo
 * @property string $status
 * @property string $note
 * @property string $partita_iva
 * @property string $iban
 * @property string $facebook
 * @property string $twitter
 * @property string $linkedin
 * @property string $googleplus
 * @property string $ultimo_accesso
 * @property string $ultimo_logout
 * @property integer $validato_almeno_una_volta
 * @property integer $avatar_id
 * @property integer $nascita_nazioni_id
 * @property integer $nascita_province_id
 * @property integer $nascita_comuni_id
 * @property integer $user_profile_titoli_studio_id
 * @property integer $user_profile_stati_civili_id
 * @property integer $nazionalita_residenza_id
 * @property integer $comune_residenza_id
 * @property integer $provincia_residenza_id
 * @property integer $domicilio_provincia_id
 * @property integer $domicilio_comune_id
 * @property integer $residenza_nazione_id
 * @property integer $facilitatore_id
 * @property integer $default_facilitatore
 * @property integer $user_profile_area_id
 * @property string $user_profile_area_other
 * @property integer $user_profile_role_id
 * @property string $user_profile_role_other
 * @property integer $user_profile_age_group_id
 * @property integer $prevalent_partnership_id
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \lispa\amos\comuni\models\IstatComuni $comuneResidenza
 * @property \lispa\amos\comuni\models\IstatProvince $provinciaResidenza
 * @property \lispa\amos\comuni\models\IstatComuni $domicilioComune
 * @property \lispa\amos\comuni\models\IstatComuni $nascitaComuni
 * @property \lispa\amos\comuni\models\IstatProvince $domicilioProvincia
 * @property \lispa\amos\comuni\models\IstatProvince $nascitaProvince
 * @property \lispa\amos\comuni\models\IstatNazioni $nascitaNazioni
 * @property \lispa\amos\admin\models\UserProfile $facilitatore
 * @property \lispa\amos\admin\models\UserProfileArea $userProfileArea
 * @property \lispa\amos\admin\models\UserProfileRole $userProfileRole
 * @property \lispa\amos\admin\models\UserProfileAgeGroup $userProfileAgeGroup
 * @property Organization $prevalentPartnership
 * @property \lispa\amos\core\user\User $user
 */
class UserProfile extends NotifyAuditRecord
{
    // Workflow states
    const USERPROFILE_WORKFLOW = 'UserProfileWorkflow';
    const USERPROFILE_WORKFLOW_STATUS_DRAFT = 'UserProfileWorkflow/DRAFT';
    const USERPROFILE_WORKFLOW_STATUS_TOVALIDATE = 'UserProfileWorkflow/TOVALIDATE';
    const USERPROFILE_WORKFLOW_STATUS_VALIDATED = 'UserProfileWorkflow/VALIDATED';
    
    /**
     * Activated user profile value
     */
    const STATUS_ACTIVE = 1;
    
    /**
     * Deactivated user profile value
     */
    const STATUS_DEACTIVATED = 0;
    
    const BOOLEAN_FIELDS_VALUE_YES = 1;
    const BOOLEAN_FIELDS_VALUE_NO = 0;
    
    /**
     * All the scenarios listed below are for the wizard.
     */
    const SCENARIO_INTRODUCTION = 'scenario_introduction';
    const SCENARIO_INTRODUCING_MYSELF = 'scenario_introducing_myself';
    const SCENARIO_ROLE_AND_AREA = 'scenario_role_and_area';
    const SCENARIO_INTERESTS = 'scenario_interests';
    const SCENARIO_PARTNERSHIP = 'scenario_partnership';
    const SCENARIO_SUMMARY = 'scenario_summary';
    
    /**
     * Dynamic scenario for form
     */
    const SCENARIO_DYNAMIC = 'scenario_dynamic';
    
    /**
     * Scenario used in user reactivate and deactivate action.
     */
    const SCENARIO_REACTIVATE_DEACTIVATE_USER = 'scenario_reactivate_deactivate_user';
    
    /**
     * Scenario used to create new account for social login.
     */
    const SCENARIO_CREATE_NEW_ACCOUNT = 'scenario_create_new_account';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profile';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        /**
         * @var array $requiredArray
         * default required fields are :
         * [ 'nome', 'cognome', 'status', 'presentazione_breve' ]
         */
        $requiredArray = AmosAdmin::getInstance()->profileRequiredFields;

        //Administrator user may have the need to change userProfiles even if some required fields have not been set yet, if he changes his own profile, the field are required
        if(\Yii::$app->user->can('ADMIN') && $this->id !== 1){
            $requiredArray = ['nome', 'cognome'];
        }

        return [
            [$requiredArray, 'required'],
            [[
                'sesso',
                'widgets_selected',
                'nazionalita',
                'altri_dati_contatto',
                'note',
                'first_access_wizard_steps_accessed'
            ], 'string'],
            [[
                'attivo',
                'avatar_id',
                'nascita_nazioni_id',
                'user_profile_titoli_studio_id',
                'nascita_province_id',
                'nascita_comuni_id',
                'privacy',
                'domicilio_cap',
                'domicilio_provincia_id',
                'domicilio_comune_id',
                'created_by',
                'updated_by',
                'deleted_by',
                'comune_residenza_id',
                'provincia_residenza_id',
                'user_profile_stati_civili_id',
                'user_profile_area_id',
                'user_profile_role_id',
                'user_profile_age_group_id',
                'default_facilitatore',
                'prevalent_partnership_id'
            ], 'integer'],
            [[
                'nascita_data',
                'created_at',
                'updated_at',
                'deleted_at',
                'ultimo_accesso',
                'ultimo_logout',
                'status',
                'user_profile_area_id',
                'user_profile_area_other',
                'user_profile_role_id',
                'user_profile_role_other',
                'user_profile_age_group_id',
                'default_facilitatore',
                'prevalent_partnership_id'
            ], 'safe'],
            [[
                'nome',
                'cognome',
                'domicilio_indirizzo',
                'domicilio_localita',
                'email_pec',
                'fax',
                'facebook',
                'twitter',
                'linkedin',
                'googleplus',
                'numero_civico_residenza',
                'indirizzo_residenza',
                'cap_residenza',
                'status',
                'user_profile_area_other',
                'user_profile_role_other'
            ], 'string', 'max' => 255],
            // phone numbers must accept any char - in USA also letters are allowed
            [['telefono', 'cellulare'], 'string', 'max' => 16],
            [['partita_iva'], 'string', 'max' => 20],
            [['iban'], 'string', 'max' => 50],
            [['domicilio_civico'], 'string', 'max' => 10],
            [['domicilio_lat', 'domicilio_lon'], 'string', 'max' => 45],
            [['presentazione_breve'], 'string', 'max' => 140],
            [['presentazione_personale'], 'string', 'max' => 600],
            [['codice_fiscale'], 'string', 'max' => 16],
            ['codice_fiscale', 'unique'],
            [['user_profile_titoli_studio_id'], 'exist', 'skipOnError' => true, 'targetClass' => AmosAdmin::instance()->createModel('UserProfileTitoliStudio')->className(), 'targetAttribute' => ['user_profile_titoli_studio_id' => 'id']],
            [['facilitatore_id'], 'exist', 'skipOnError' => true, 'targetClass' => AmosAdmin::instance()->createModel('UserProfile')->className(), 'targetAttribute' => ['facilitatore_id' => 'id']],
            [['residenza_nazione_id'], 'exist', 'skipOnError' => true, 'targetClass' => AmosAdmin::instance()->createModel('IstatNazioni')->className(), 'targetAttribute' => ['residenza_nazione_id' => 'id']],
            [['nascita_nazioni_id'], 'exist', 'skipOnError' => true, 'targetClass' => AmosAdmin::instance()->createModel('IstatNazioni')->className(), 'targetAttribute' => ['nascita_nazioni_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => AmosAdmin::instance()->createModel('User')->className(), 'targetAttribute' => ['user_id' => 'id']],
            [['user_profile_stati_civili_id'], 'exist', 'skipOnError' => true, 'targetClass' => AmosAdmin::instance()->createModel('UserProfileStatiCivili')->className(), 'targetAttribute' => ['user_profile_stati_civili_id' => 'id']],
            [['user_profile_role_other'], 'required', 'when' => function ($model) {
                return ($this->user_profile_role_id == \lispa\amos\admin\models\UserProfileRole::OTHER);
            }, 'whenClient' => "function (attribute, value) {
                return ($('#" . Html::getInputId($this, 'user_profile_role_id') . "').val() == " . \lispa\amos\admin\models\UserProfileRole::OTHER . ");
            }"],
            [['user_profile_area_other'], 'required', 'when' => function ($model) {
                return ($this->user_profile_area_id == \lispa\amos\admin\models\UserProfileArea::OTHER);
            }, 'whenClient' => "function (attribute, value) {
                return ($('#" . Html::getInputId($this, 'user_profile_area_id') . "').val() == " . \lispa\amos\admin\models\UserProfileArea::OTHER . ");
            }"],
            [['facilitatore_id'], 'required', 'when' => function ($model) {
                return ($this->status != self::USERPROFILE_WORKFLOW_STATUS_DRAFT);
            }],
            [['privacy'], 'checkPrivacy']
        ];

    }
    
    /**
     * Custom validation form "privacy" field
     */
    public function checkPrivacy()
    {
        if (!$this->privacy && !\Yii::$app->user->can('ADMIN')) {
            $this->addError('privacy', AmosAdmin::t('amosadmin', "#check_user_profile_privacy_message"));
        }
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        /** @var AmosAdmin $adminModule */
        $adminModule = \Yii::$app->getModule(AmosAdmin::getModuleName());
        
        $scenarios = parent::scenarios();

        $dynamicScenarioFields = $adminModule->confManager->getFormFields();

        $scenarios[self::SCENARIO_INTRODUCTION] = $this->createScenarioArray([
        ], $dynamicScenarioFields);
        
        $scenarios[self::SCENARIO_INTRODUCING_MYSELF] = $this->createScenarioArray([
            'presentazione_breve',
            'sesso',
            'user_profile_age_group_id',
            'facilitatore_id'
        ], $dynamicScenarioFields);
        
        $scenarios[self::SCENARIO_ROLE_AND_AREA] = $this->createScenarioArray([
            'user_profile_area_id',
            'user_profile_area_other',
            'user_profile_role_id',
            'user_profile_role_other'
        ], $dynamicScenarioFields);
        
        $scenarios[self::SCENARIO_INTERESTS] = $this->createScenarioArray([
        ], $dynamicScenarioFields);
        
        $scenarios[self::SCENARIO_PARTNERSHIP] = $this->createScenarioArray([
            'prevalent_partnership_id',
            'status'
        ], $dynamicScenarioFields);
        
        $scenarios[self::SCENARIO_REACTIVATE_DEACTIVATE_USER] = [
            'attivo'
        ];
        
        $scenarios[self::SCENARIO_CREATE_NEW_ACCOUNT] = [
            'user_id',
            'attivo',
            'status',
            'nome',
            'cognome',
            'widgets_selected'
        ];

        $scenarios[self::SCENARIO_DYNAMIC] = $dynamicScenarioFields;
        
        return $scenarios;
    }

    private function createScenarioArray($scenarioFields, $dynamicScenarioFields){
        $newScenarioFields = [];
        foreach ($scenarioFields as $scenarioField){
            if(in_array($scenarioField,$dynamicScenarioFields)){
                $newScenarioFields[] = $scenarioField;
            }
        }
        return $newScenarioFields;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => AmosAdmin::t('amosadmin', 'ID'),
            'nome' => AmosAdmin::t('amosadmin', 'Nome'),
            'cognome' => AmosAdmin::t('amosadmin', 'Cognome'),
            'sesso' => AmosAdmin::t('amosadmin', 'Sesso'),
            'presentazione_breve' => AmosAdmin::t('amosadmin', 'Presentazione Breve'),
            'codice_fiscale' => AmosAdmin::t('amosadmin', 'Codice fiscale'),
            'avatar_id' => AmosAdmin::t('amosadmin', 'Immagine profilo'),
            'nascita_data' => AmosAdmin::t('amosadmin', 'Data di nascita'),
            'nascita_nazioni_id' => AmosAdmin::t('amosadmin', 'Nazione di nascita'),
            'nascita_province_id' => AmosAdmin::t('amosadmin', 'Provincia di nascita'),
            'nascita_comuni_id' => AmosAdmin::t('amosadmin', 'Comune di nascita'),
            'privacy' => AmosAdmin::t('amosadmin', 'Accettazione privacy'),
            'domicilio_indirizzo' => AmosAdmin::t('amosadmin', 'Indirizzo'),
            'domicilio_civico' => AmosAdmin::t('amosadmin', 'Civico'),
            'domicilio_cap' => AmosAdmin::t('amosadmin', 'Cap'),
            'user_profile_stati_civili_id' => AmosAdmin::t('amosadmin', 'Stato civile'),
            'domicilio_provincia_id' => AmosAdmin::t('amosadmin', 'Provincia'),
            'domicilio_comune_id' => AmosAdmin::t('amosadmin', 'Comune'),
            'facilitatore_id' => AmosAdmin::t('amosadmin', 'Facilitatore'),
            'domicilio_localita' => AmosAdmin::t('amosadmin', 'Frazione'),
            'altri_dati_contatto' => AmosAdmin::t('amosadmin', 'Altri dati di contatto'),
            'domicilio_lat' => AmosAdmin::t('amosadmin', 'Domicilio latitudine'),
            'domicilio_lon' => AmosAdmin::t('amosadmin', 'Longitudine'),
            'residenza_nazione_id' => AmosAdmin::t('amosadmin', 'Nazione'),
            'partita_iva' => AmosAdmin::t('amosadmin', 'Partita IVA'),
            'iban' => AmosAdmin::t('amosadmin', 'IBAN'),
            'user_id' => AmosAdmin::t('amosadmin', 'User'),
            'email_pec' => AmosAdmin::t('amosadmin', 'Email (PEC)'),
            'user_profile_titoli_studio_id' => AmosAdmin::t('amosadmin', 'Titolo di studio'),
            'indirizzo_residenza' => AmosAdmin::t('amosadmin', 'Indirizzo'),
            'numero_civico_residenza' => AmosAdmin::t('amosadmin', 'Civico'),
            'cap_residenza' => AmosAdmin::t('amosadmin', 'Cap'),
            'provincia_residenza_id' => AmosAdmin::t('amosadmin', 'Provincia'),
            'comune_residenza_id' => AmosAdmin::t('amosadmin', 'Comune'),
            'widgets_selected' => AmosAdmin::t('amosadmin', 'Widgets selezionati'),
            'first_access_wizard_steps_accessed' => AmosAdmin::t('amosadmin', 'Passi aperti in first access wizard'),
            'nazionalita' => AmosAdmin::t('amosadmin', 'Nazionalità'),
            'telefono' => AmosAdmin::t('amosadmin', 'Telefono'),
            'fax' => AmosAdmin::t('amosadmin', 'FAX'),
            'cellulare' => AmosAdmin::t('amosadmin', 'Cellulare'),
            'status' => AmosAdmin::t('amosadmin', 'Stato profilo utente'),
            'facebook' => AmosAdmin::t('amosadmin', 'Profilo Facebook'),
            'twitter' => AmosAdmin::t('amosadmin', 'Profilo Twitter'),
            'linkedin' => AmosAdmin::t('amosadmin', 'Profilo Linkedin'),
            'googleplus' => AmosAdmin::t('amosadmin', 'Profilo Google Plus'),
            'validato_almeno_una_volta' => AmosAdmin::t('amosadmin', 'L\'utente è stato validato almeno una volta'),
            'note' => AmosAdmin::t('amosadmin', 'Annotazioni'),
            'presentazione_personale' => AmosAdmin::t('amosadmin', 'Presentazione personale'),
            'user_profile_area_id' => AmosAdmin::t('amosadmin', 'Area Id'),
            'area' => AmosAdmin::t('amosadmin', '#faw_rea_area'),
            'user_profile_area_other' => AmosAdmin::t('amosadmin', 'Other Area'),
            'user_profile_role_id' => AmosAdmin::t('amosadmin', 'Role Id'),
            'role' => AmosAdmin::t('amosadmin', '#faw_rea_role'),
            'user_profile_role_other' => AmosAdmin::t('amosadmin', 'Other Role'),
            'user_profile_age_group_id' => AmosAdmin::t('amosadmin', 'Age Group Id'),
            'age_group' => AmosAdmin::t('amosadmin', 'Age Group'),
            'prevalent_partnership_id' => AmosAdmin::t('amosadmin', 'Prevalent Partnership Id'),
            'prevalentPartnership' => AmosAdmin::t('amosadmin', 'Prevalent Partnership'),
            'created_at' => AmosAdmin::t('amosadmin', 'Creato il'),
            'updated_at' => AmosAdmin::t('amosadmin', 'Aggiornato il'),
            'deleted_at' => AmosAdmin::t('amosadmin', 'Cancellato il'),
            'created_by' => AmosAdmin::t('amosadmin', 'Creato da'),
            'updated_by' => AmosAdmin::t('amosadmin', 'Aggiornato da'),
            'deleted_by' => AmosAdmin::t('amosadmin', 'Cancellato da'),
            
            'nomeCognome' => AmosAdmin::t('amosadmin', 'Name and surname'),
            'facilitatore' => AmosAdmin::t('amosadmin', 'Facilitator'),
            'surnameName' => AmosAdmin::t('amosadmin', 'Surname and name'),
        ]);
    }
    
//    /**
//     * @inheritdoc
//     */
//    public function attributeHints()
//    {
//        return ArrayHelper::merge(parent::attributeHints(), [
//            'sesso' => AmosAdmin::t('amosadmin', 'These data will not be shown to other users'),
//            'user_profile_area_id' => AmosAdmin::t('amosadmin', 'These data will not be shown to other users'),
//            'area' => AmosAdmin::t('amosadmin', 'These data will not be shown to other users'),
//            'user_profile_role_id' => AmosAdmin::t('amosadmin', 'These data will not be shown to other users'),
//            'role' => AmosAdmin::t('amosadmin', 'These data will not be shown to other users'),
//            'user_profile_age_group_id' => AmosAdmin::t('amosadmin', 'These data will not be shown to other users'),
//            'age_group' => AmosAdmin::t('amosadmin', 'These data will not be shown to other users'),
//        ]);
//    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomicilioComune()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatComuni')->className(), ['id' => 'domicilio_comune_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomicilioProvincia()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatProvince')->className(), ['id' => 'domicilio_provincia_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfileTagMms()
    {
        return $this->hasMany(AmosAdmin::instance()->createModel('UserProfileTagMm')->className(), ['user_profile_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(\lispa\amos\tag\models\Tag::className(), ['id' => 'tag_id'])->viaTable('user_profile_tag_mm', ['user_profile_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNascitaComuni()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatComuni')->className(), ['id' => 'nascita_comuni_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNascitaProvince()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatProvince')->className(), ['id' => 'nascita_province_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNascitaNazioni()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatNazioni')->className(), ['id' => 'nascita_nazioni_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('User')->className(), ['id' => 'user_id'])->inverseOf('userProfile');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResidenzaNazione()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatNazioni')->className(), ['id' => 'residenza_nazione_id'])->inverseOf('userProfile');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvinciaResidenza()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatProvince')->className(), ['id' => 'provincia_residenza_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComuneResidenza()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('IstatComuni')->className(), ['id' => 'comune_residenza_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfileStatiCivili()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('UserProfileStatiCivili')->className(), ['id' => 'user_profile_stati_civili_id'])->inverseOf('userProfile');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacilitatore()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('UserProfile')->className(), ['id' => 'facilitatore_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfileTitoliStudio()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('UserProfileTitoliStudio')->className(), ['id' => 'user_profile_titoli_studio_id'])->inverseOf('userProfile');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfileArea()
    {
        return $this->hasOne(\lispa\amos\admin\models\UserProfileArea::className(), ['id' => 'user_profile_area_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfileRole()
    {
        return $this->hasOne(\lispa\amos\admin\models\UserProfileRole::className(), ['id' => 'user_profile_role_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfileAgeGroup()
    {
        return $this->hasOne(\lispa\amos\admin\models\UserProfileAgeGroup::className(), ['id' => 'user_profile_age_group_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrevalentPartnership()
    {
        $admin =  AmosAdmin::getInstance();
        /** @var  $organizationsModule OrganizationsModuleInterface*/
        $organizationsModule = \Yii::$app->getModule($admin->getOrganizationModuleName());
        if (!is_null($organizationsModule)) {
            return $this->hasOne($organizationsModule->getOrganizationModelClass(), ['id' => 'prevalent_partnership_id']);
        } else {
            return null;
        }
    }
    
    /**
     * @return array
     */
    public function getSexValues()
    {
        return [
            'None' => AmosAdmin::t('amosadmin', 'Non Definito'),
            'Maschio' => AmosAdmin::t('amosadmin', 'Male'),
            'Femmina' => AmosAdmin::t('amosadmin', 'Female')
        ];
    }
    
    /**
     * @return array
     */
    public function getSexValuesForSelect()
    {
        return ArrayHelper::merge([
            '-1' => AmosAdmin::t('amosadmin', 'Not selected')
        ], $this->getSexValues());
    }
}
