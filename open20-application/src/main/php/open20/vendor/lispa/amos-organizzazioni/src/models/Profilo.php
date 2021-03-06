<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni\models
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\models;

use lispa\amos\attachments\behaviors\FileBehavior;
use lispa\amos\core\interfaces\ContentModelInterface;
use lispa\amos\core\interfaces\OrganizationsModelInterface;
use lispa\amos\core\validators\CfPivaValidator;
use lispa\amos\core\validators\PIVAValidator;
use lispa\amos\cwh\base\ModelNetworkInterface;
use lispa\amos\cwh\models\CwhConfig;
use lispa\amos\organizzazioni\components\OrganizationsPlacesComponents;
use lispa\amos\organizzazioni\Module;
use lispa\amos\organizzazioni\widgets\icons\WidgetIconProfilo;
use lispa\amos\organizzazioni\widgets\UserNetworkWidget;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Profilo
 * This is the model class for table "profilo".
 * @package lispa\amos\organizzazioni\models
 */
class Profilo extends \lispa\amos\organizzazioni\models\base\Profilo implements ContentModelInterface, ModelNetworkInterface, OrganizationsModelInterface
{
    private $logoOrganization;
    private $allegati;
    protected $places_fields = ['sede_legale_indirizzo', 'indirizzo'];

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            //inserire il campo o i campi rappresentativi del modulo
            'name',
//            'la_sede_legale_e_la_stessa_del',
//            'rappresentante_legale',
//            'referente_operativo',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        /* return [
          'denominazione' => Module::t('amosorganizzazioni', ''),
          'partita_iva' => Module::t('amosorganizzazioni', 'Deve essere obbligatorio ma alternativi a cf'),
          'codice_fiscale' => Module::t('amosorganizzazioni', 'Deve essere obbligatorio ma alternativi a p.iva'),
          'presentazione_della_organizzaz' => Module::t('amosorganizzazioni', ''),
          'principali_ambiti_di_attivita_organizzazione' => Module::t('amosorganizzazioni', 'deve essere di tipo albero identico a quello del plugin della POI'),
          'ambiti_tecnologici_su_cui_siet' => Module::t('amosorganizzazioni', 'deve essere di tipo albero identico a quello del plugin della POI'),
          'tipologia_di_organizzazione' => Module::t('amosorganizzazioni', 'deve essere una tendina le cui opzioni mi verranno confermate ma tendenzialmente uguali a POI'),
          'forma_legale' => Module::t('amosorganizzazioni', 'deve essere una tendina le cui opzioni mi verranno confermate ma tendenzialmente uguali a POI'),
          'sito_web' => Module::t('amosorganizzazioni', ''),
          'facebook' => Module::t('amosorganizzazioni', ''),
          'twitter' => Module::t('amosorganizzazioni', ''),
          'linkedin' => Module::t('amosorganizzazioni', ''),
          'google' => Module::t('amosorganizzazioni', ''),
          'logo' => Module::t('amosorganizzazioni', 'upload di immagine'),
          'allegati' => Module::t('amosorganizzazioni', 'upload di file'),
          'indirizzo' => Module::t('amosorganizzazioni', 'deve essere come quello di POI cioè con la mappa'),
          'telefono' => Module::t('amosorganizzazioni', ''),
          'fax' => Module::t('amosorganizzazioni', ''),
          'email' => Module::t('amosorganizzazioni', ''),
          'pec' => Module::t('amosorganizzazioni', ''),
          'la_sede_legale_e_la_stessa_del' => Module::t('amosorganizzazioni', 'si/no'),
          'sede_legale_indirizzo' => Module::t('amosorganizzazioni', 'deve essere come quello di POI cioè con la mappa e deve comparire solo se alla domanda precedente è stato risposto no'),
          'sede_legale_telefono' => Module::t('amosorganizzazioni', ' deve comparire solo se alla domanda precedente è stato risposto no'),
          'sede_legale_fax' => Module::t('amosorganizzazioni', ' deve comparire solo se alla domanda precedente è stato risposto no'),
          'sede_legale_email' => Module::t('amosorganizzazioni', ' deve comparire solo se alla domanda precedente è stato risposto no'),
          'sede_legale_pec' => Module::t('amosorganizzazioni', ' deve comparire solo se alla domanda precedente è stato risposto no'),
          'responsabile' => Module::t('amosorganizzazioni', 'da selezionare tra gli utenti registrati'),
          'rappresentante_legale' => Module::t('amosorganizzazioni', 'da selezionare tra gli utenti registrati'),
          'referente_operativo' => Module::t('amosorganizzazioni', 'da selezionare tra gli utenti registrati'),
          ]; */
        return [];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
            [
                'fileBehavior' => [
                    'class' => FileBehavior::className()
                ],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->on(self::EVENT_BEFORE_VALIDATE, [
            $this,
            'organizationsBeforeValidate'
        ]);

        parent::init();
    }

    public function organizationsBeforeValidate()
    {
        //$places_fields = ['sede_legale_indirizzo', 'indirizzo'];

        foreach ($this->places_fields as $place_field) {
            $place_id = $this->$place_field;
            OrganizationsPlacesComponents::checkPlace($place_id);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        //$places_fields = ['sede_legale_indirizzo', 'indirizzo'];
        foreach ($this->places_fields as $place_field) {
            $place_id = $this->$place_field;
            OrganizationsPlacesComponents::checkPlace($place_id);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Returns the address in string format
     * @return string
     */
    public function getAddressString($field_name){
        //check if the input $field_name is in the class array containg the address's fields
        if(!isset($this->$field_name) && !in_array($field_name, $this->places_fields)){
            return false;
        }
        //gets the record by the input field
        $placeObj = OrganizationsPlacesComponents::getPlace($this->$field_name);
        //return the address's string
        return OrganizationsPlacesComponents::getGeocodeString($placeObj);
    }

    /**
     * Returns the text hint for the specified attribute.
     * @param string $attribute the attribute name
     * @return string the attribute hint
     */
    public function getAttributeHint($attribute)
    {
        $hints = $this->attributeHints();
        return isset($hints[$attribute]) ? $hints[$attribute] : null;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['partita_iva'], PIVAValidator::className()],
//                [['partita_iva'], 'string', 'length' => 11],
                [['codice_fiscale'], CfPivaValidator::className()],
//                [['codice_fiscale'], 'string', 'length' => 11],
                [['email'], 'email'],
                [['pec'], 'email'],
                [['sede_legale_email'], 'email'],
                [['sede_legale_pec'], 'email'],]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), []);
    }

    /**
     * Verifica la validità del codice fiscale (numerico) o della partita iva
     * @param string $attribute
     * @param array $params
     */
    public function checkPartitaIva($attribute, $params)
    {
        $partitaIva = $this->$attribute;
        $isValid = false;
        if (!$partitaIva) {
            $isValid = true;
        } else if (strlen($partitaIva) != 11) {
            $isValid = false;
        } else if (strlen($partitaIva) == 11) {
            //la p.iva deve avere solo cifre
            if (!preg_match("/^[0-9]+$/i", $partitaIva)) {
                $isValid = false;
            } else {
                $primo = 0;
                for ($i = 0; $i <= 9; $i += 2) {
                    $primo += ord($partitaIva[$i]) - ord('0');
                }

                for ($i = 1; $i <= 9; $i += 2) {
                    $secondo = 2 * (ord($partitaIva[$i]) - ord('0'));

                    if ($secondo > 9) $secondo = $secondo - 9;
                    $primo += $secondo;
                }
                if ((10 - $primo % 10) % 10 != ord($partitaIva[10]) - ord('0')) {
                    $isValid = false;
                } else {
                    $isValid = true;
                }
            }
        }
        if (!$isValid) {
            $this->addError($attribute, Module::t('amoscore', 'The TAX code/VAT code is not in a permitted format'));
        }
    }

    /**
     * @return array
     */
    public static function getEditFields()
    {
        $labels = self::attributeLabels();

        return [
            [
                'slug' => 'name',
                'label' => $labels['name'],
                'type' => 'string'
            ],
            [
                'slug' => 'partita_iva',
                'label' => $labels['partita_iva'],
                'type' => 'string'
            ],
            [
                'slug' => 'codice_fiscale',
                'label' => $labels['codice_fiscale'],
                'type' => 'string'
            ],
            [
                'slug' => 'presentazione_della_organizzaz',
                'label' => $labels['presentazione_della_organizzaz'],
                'type' => 'text'
            ],
            [
                'slug' => 'tipologia_di_organizzazione',
                'label' => $labels['tipologia_di_organizzazione'],
                'type' => 'string'
            ],
            [
                'slug' => 'forma_legale',
                'label' => $labels['forma_legale'],
                'type' => 'string'
            ],
            [
                'slug' => 'sito_web',
                'label' => $labels['sito_web'],
                'type' => 'string'
            ],
            [
                'slug' => 'facebook',
                'label' => $labels['facebook'],
                'type' => 'string'
            ],
            [
                'slug' => 'twitter',
                'label' => $labels['twitter'],
                'type' => 'string'
            ],
            [
                'slug' => 'linkedin',
                'label' => $labels['linkedin'],
                'type' => 'string'
            ],
            [
                'slug' => 'google',
                'label' => $labels['google'],
                'type' => 'string'
            ],
            [
                'slug' => 'indirizzo',
                'label' => $labels['indirizzo'],
                'type' => 'string'
            ],
            [
                'slug' => 'telefono',
                'label' => $labels['telefono'],
                'type' => 'decimal'
            ],
            [
                'slug' => 'fax',
                'label' => $labels['fax'],
                'type' => 'decimal'
            ],
            [
                'slug' => 'email',
                'label' => $labels['email'],
                'type' => 'string'
            ],
            [
                'slug' => 'pec',
                'label' => $labels['pec'],
                'type' => 'string'
            ],
            [
                'slug' => 'la_sede_legale_e_la_stessa_del',
                'label' => $labels['la_sede_legale_e_la_stessa_del'],
                'type' => 'string'
            ],
            [
                'slug' => 'sede_legale_indirizzo',
                'label' => $labels['sede_legale_indirizzo'],
                'type' => 'string'
            ],
            [
                'slug' => 'sede_legale_telefono',
                'label' => $labels['sede_legale_telefono'],
                'type' => 'decimal'
            ],
            [
                'slug' => 'sede_legale_fax',
                'label' => $labels['sede_legale_fax'],
                'type' => 'decimal'
            ],
            [
                'slug' => 'sede_legale_email',
                'label' => $labels['sede_legale_email'],
                'type' => 'string'
            ],
            [
                'slug' => 'sede_legale_pec',
                'label' => $labels['sede_legale_pec'],
                'type' => 'string'
            ],
            [
                'slug' => 'responsabile',
                'label' => $labels['responsabile'],
                'type' => 'string'
            ],
            [
                'slug' => 'rappresentante_legale',
                'label' => $labels['rappresentante_legale'],
                'type' => 'string'
            ],
            [
                'slug' => 'referente_operativo',
                'label' => $labels['referente_operativo'],
                'type' => 'string'
            ],
        ];
    }

    public function getLogoOrganization()
    {
        if (empty($this->logoOrganization)) {
            $this->logoOrganization = $this->hasOneFile('logoOrganization')->one();
        }
        return $this->logoOrganization;
    }

    public function getAllegati()
    {
        if (empty($this->allegati)) {
            $this->allegati = $this->hasMultipleFiles('allegati')->one();
        }
        return $this->allegati;
    }

    /**
     * @param $logo
     */
    public function setLogoOrganization($logo)
    {
        $this->logoOrganization = $logo;
    }

    /**
     * @param $allegati
     */
    public function setAllegati($allegati)
    {
        $this->allegati = $allegati;
    }

    /**
     * @return string The model title field value
     */
    public function getTitle()
    {
        return $this->name;
    }

    /**
     * @return string The model description field value
     */
    public function getDescription($truncate)
    {
        $ret = $this->name;

        if ($truncate) {
            $ret = $this->__shortText($this->name, 200);
        }
        return $ret;
    }

    /**
     * @return array The columns ti show as default in GridViewWidget
     */
    public function getGridViewColumns()
    {
        return [];
    }

    /**
     * @return DateTime date begin of publication
     */
    public function getPublicatedFrom()
    {
        return null;
    }

    /**
     * @return DateTime date end of publication
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
        return null;
    }

    /**
     * @return string The classname of the generic dashboard widget to access the plugin
     */
    public function getPluginWidgetClassname()
    {
        return WidgetIconProfilo::className();
    }

    /**
     * @return mixed
     */
    public function getGrammar()
    {
        // TODO: Implement getGrammar() method.
    }

    /**
     * @return string The name that correspond to 'to validate' status for the content model
     */
    public function getToValidateStatus()
    {
        // TODO: Implement getToValidateStatus() method.
    }

    /**
     * @return string The name that correspond to 'published' status for the content model
     */
    public function getValidatedStatus()
    {
        // TODO: Implement getValidatedStatus() method.
    }

    /**
     * @return string The name that correspond to 'draft' status for the content model
     */
    public function getDraftStatus()
    {
        // TODO: Implement getDraftStatus() method.
    }

    /**
     * @return string The name of model validator role
     */
    public function getValidatorRole()
    {
        // TODO: Implement getValidatorRole() method.
    }

    /**
     * Get the user id used in network-users association table
     * @return int
     */
    public function getUserId()
    {
        return Yii::$app->getUser()->id;
    }

    /**
     * Get the name of the table storing network-users associations
     * @return string
     */
    public function getMmTableName()
    {
        return ProfiloUserMm::tableName();
    }

    /**
     * Get the name of field that contains user id in network-users association table
     * @return string
     */
    public function getMmNetworkIdFieldName()
    {
        return 'profilo_id';
    }

    /**
     * Get the name of field that contains network id in network-users association table
     * @return string
     */
    public function getMmUserIdFieldName()
    {
        return 'user_id';
    }

    /**
     * Return true if the user with id $userId belong to the network with id $networkId; if $userId is null the logged User id is considered
     * @param  int $networkId
     * @param int $userId
     * @return bool
     */
    public function isNetworkUser($networkId, $userId = null)
    {
        if (!isset($userId)) {
            $userId = $this->getUserId();
        }
        $mmRow = ProfiloUserMm::findOne([
            $this->getMmNetworkIdFieldName() => $networkId,
            $this->getMmUserIdFieldName() => $userId,
        ]);
        if (!is_null($mmRow)) {
            return true;
        }
        return false;
    }

    /**
     * Return true if the network is validated or no validation process is implemented for the network.
     * if $networkId is null, current network (this) is condidered
     * @param int $networkId
     * @return bool
     */
    public function isValidated($networkId = null)
    {
        return true;
    }

    /**
     * Return classname of the MM table connecting user and network
     * @return string
     */
    public function getMmClassName()
    {
        return ProfiloUserMm::className();
    }

    /**
     * @param null $userId
     * @param bool $isUpdate
     * @return mixed
     */
    public function getUserNetworkWidget($userId = null, $isUpdate = false)
    {

        return UserNetworkWidget::widget(['userId' => $userId, 'isUpdate' => $isUpdate]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSedeIndirizzo()
    {
        return $this->hasOne(OrganizationsPlaces::className(), ['place_id' => 'indirizzo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSedeLegaleIndirizzo()
    {
        return $this->hasOne(OrganizationsPlaces::className(), ['place_id' => 'sede_legale_indirizzo']);
    }

    /**
     * @return array list of statuses that for cwh is validated
     */
    public function getCwhValidationStatuses()
    {
        return [$this->getValidatedStatus()];
    }

    /**
     * @param null $userId
     * @return mixed
     */
    public function getUserNetworkAssociationQuery($userId = null)
    {
        if (empty($userId)) {
            $userId = Yii::$app->user->id;
        }
        $query = self::find()->distinct();
        $queryJoined = Profilo::find()->distinct();
        $queryJoined->innerJoin(\lispa\amos\organizzazioni\models\ProfiloUserMm::tableName(),
            Profilo::tableName() . '.id = ' . ProfiloUserMm::tableName() . '.profilo_id'
            . ' AND ' . ProfiloUserMm::tableName() . '.user_id = ' . $userId)
            ->andWhere(ProfiloUserMm::tableName() . '.deleted_at is null');
        $queryJoined->select(Profilo::tableName() . '.id');
        $query->andWhere(['not in', Profilo::tableName() . '.id', $queryJoined]);
        $query->andWhere(Profilo::tableName() . '.deleted_at is null');
        return $query;
    }

    /**
     * Get Id of configuration record for network model Profilo
     * @return int $cwhConfigId
     */
    public static function getCwhConfigId()
    {
        // Default network configuration id = 7 for organizzazioni
        $cwhConfigId = 7;
        $cwhConfig = CwhConfig::findOne(['tablename' => self::tableName()]);
        if (!is_null($cwhConfig)) {
            $cwhConfigId = $cwhConfig->id;
        }
        return $cwhConfigId;
    }

    public function getNameField()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAddressField()
    {
        if (!empty($this->sedeIndirizzo)) {
            return '(' . $this->sedeIndirizzo->postal_code . ')'
                . ' ' . $this->sedeIndirizzo->region
                . ' ' . $this->sedeIndirizzo->city
                . ' ' . $this->sedeIndirizzo->address
                . ' ' . $this->sedeIndirizzo->street_number;
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getAddressFieldSedeLegale()
    {
        if (!empty($this->sedeLegaleIndirizzo)) {
            return '(' . $this->sedeLegaleIndirizzo->postal_code . ')'
                . ' ' . $this->sedeLegaleIndirizzo->region
                . ' ' . $this->sedeLegaleIndirizzo->city
                . ' ' . $this->sedeLegaleIndirizzo->address
                . ' ' . $this->sedeLegaleIndirizzo->street_number;
        } else {
            return '';
        }
    }
}
