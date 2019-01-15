<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni\models\base
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\models\base;

use lispa\amos\organizzazioni\Module;
use yii\helpers\ArrayHelper;

/**
 * Class Profilo
 *
 * This is the base-model class for table "profilo".
 *
 * @property integer $id
 * @property string $name
 * @property string $partita_iva
 * @property string $codice_fiscale
 * @property string $presentazione_della_organizzaz
 * @property string $principali_ambiti_di_attivita_
 * @property string $ambiti_tecnologici_su_cui_siet
 * @property string $tipologia_di_organizzazione
 * @property string $forma_legale
 * @property string $sito_web
 * @property string $facebook
 * @property string $twitter
 * @property string $linkedin
 * @property string $google
 * @property string $indirizzo
 * @property string $telefono
 * @property string $fax
 * @property string $email
 * @property string $pec
 * @property string $la_sede_legale_e_la_stessa_del
 * @property string $sede_legale_indirizzo
 * @property string $sede_legale_telefono
 * @property string $sede_legale_fax
 * @property string $sede_legale_email
 * @property string $sede_legale_pec
 * @property string $responsabile
 * @property string $rappresentante_legale
 * @property string $referente_operativo
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @package lispa\amos\organizzazioni\models\base
 */
class Profilo extends \lispa\amos\core\record\Record
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profilo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'indirizzo', 'telefono', 'email', 'la_sede_legale_e_la_stessa_del', 'responsabile', 'rappresentante_legale', 'referente_operativo'], 'required'],
            [['presentazione_della_organizzaz','telefono', 'fax', 'sede_legale_telefono', 'sede_legale_fax'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['name', 'partita_iva', 'codice_fiscale', 'tipologia_di_organizzazione', 'forma_legale', 'sito_web', 'facebook', 'twitter', 'linkedin', 'google', 'indirizzo', 'email', 'pec', 'la_sede_legale_e_la_stessa_del', 'sede_legale_indirizzo', 'sede_legale_email', 'sede_legale_pec', 'responsabile', 'rappresentante_legale', 'referente_operativo'], 'string', 'max' => 255],
            [['logoOrganization'], 'file', 'extensions' => 'jpeg, jpg, png, gif', 'maxFiles' => 1],
            [['allegati'], 'file', 'maxFiles' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => Module::t('amosorganizzazioni', 'ID'),
            'name' => Module::t('amosorganizzazioni', 'Denominazione'),
            'partita_iva' => Module::t('amosorganizzazioni', 'Partita Iva'),
            'codice_fiscale' => Module::t('amosorganizzazioni', 'Codice Fiscale'),
            'presentazione_della_organizzaz' => Module::t('amosorganizzazioni', 'Presentazione della organizzazione in inglese'),
            'tipologia_di_organizzazione' => Module::t('amosorganizzazioni', 'Tipologia di organizzazione'),
            'forma_legale' => Module::t('amosorganizzazioni', 'Forma legale'),
            'sito_web' => Module::t('amosorganizzazioni', 'Sito web'),
            'facebook' => Module::t('amosorganizzazioni', 'Facebook'),
            'twitter' => Module::t('amosorganizzazioni', 'Twitter'),
            'linkedin' => Module::t('amosorganizzazioni', 'Linkedin'),
            'google' => Module::t('amosorganizzazioni', 'Google+'),
            'indirizzo' => Module::t('amosorganizzazioni', 'Indirizzo'),
            'addressField' => Module::t('amosorganizzazioni', 'Indirizzo'),
            'telefono' => Module::t('amosorganizzazioni', 'Telefono'),
            'fax' => Module::t('amosorganizzazioni', 'Fax'),
            'email' => Module::t('amosorganizzazioni', 'Email'),
            'pec' => Module::t('amosorganizzazioni', 'PEC'),
            'la_sede_legale_e_la_stessa_del' => Module::t('amosorganizzazioni', 'La sede legale è la stessa della sede operati'),
            'sede_legale_indirizzo' => Module::t('amosorganizzazioni', 'Sede legale indirizzo'),
            'sede_legale_telefono' => Module::t('amosorganizzazioni', 'Sede legale telefono'),
            'sede_legale_fax' => Module::t('amosorganizzazioni', 'Sede legale fax'),
            'sede_legale_email' => Module::t('amosorganizzazioni', 'Sede legale email'),
            'sede_legale_pec' => Module::t('amosorganizzazioni', 'Sede legale PEC'),
            'responsabile' => Module::t('amosorganizzazioni', 'Responsabile'),
            'rappresentante_legale' => Module::t('amosorganizzazioni', 'Rappresentante legale'),
            'referente_operativo' => Module::t('amosorganizzazioni', 'Referente operativo'),
            'created_at' => Module::t('amosorganizzazioni', 'Creato il'),
            'updated_at' => Module::t('amosorganizzazioni', 'Aggiornato il'),
            'deleted_at' => Module::t('amosorganizzazioni', 'Cancellato il'),
            'created_by' => Module::t('amosorganizzazioni', 'Creato da'),
            'updated_by' => Module::t('amosorganizzazioni', 'Aggiornato da'),
            'deleted_by' => Module::t('amosorganizzazioni', 'Cancellato da'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRappresentanteLegale()
    {
        return $this->hasOne(\lispa\amos\admin\models\UserProfile::className(), ['id' => 'rappresentante_legale']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferenteOperativo()
    {
        return $this->hasOne(\lispa\amos\admin\models\UserProfile::className(), ['id' => 'referente_operativo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipologiaDiOrganizzazione()
    {
        return $this->hasOne(ProfiloTypesPmi::className(), ['id' => 'tipologia_di_organizzazione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormaLegale()
    {
        return $this->hasOne(\lispa\amos\organizzazioni\models\ProfiloLegalForm::className(), ['id' => 'forma_legale']);
    }

}
