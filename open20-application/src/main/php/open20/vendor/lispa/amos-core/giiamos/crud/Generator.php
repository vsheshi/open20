<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\giiamos\crud
 * @category   CategoryName
 */

namespace lispa\amos\core\giiamos\crud;

use Yii;
use yii\db\ActiveRecord;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

class Generator extends \schmunk42\giiant\generators\crud\Generator {

    public $formTabs;
    public $templates = ['default' => '@vendor/lispa/amos-core/giiamos/crud/default', 'wizard' => '@vendor/lispa/amos-core/giiamos/crud/wizard'];
    public $template = 'default';
    public $formTabsSeparator = '|';
    public $formTabsFieldSeparator = ',';
    public $tabsFieldList = [];
    public $providerList = 'lispa\amos\core\giiamos\crud\providers\CallbackProvider,
                            lispa\amos\core\giiamos\crud\providers\DateTimeProvider,
                            lispa\amos\core\giiamos\crud\providers\EditorProvider,
                            lispa\amos\core\giiamos\crud\providers\OptsProvider,
                            lispa\amos\core\giiamos\crud\providers\RelationProvider';

    /**
     * @var array Array delle relazioni M2M
     */
    public $mmRelations = [];

    /**
     * @var array Array dei campi da visualizzare nella index 
     */
    public $campiIndex = [];

    /**
     * Descriptive name of the module
     * @var string
     */
    public $descriptiveNameModule;

    /**
     * Ordinals of the fields/relations in the wizard
     * @var array
     */
    public $ordinalFields = [];

    /**
     * @inheritdoc
     */

    public function getName() {
        return 'Amos CRUD Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription() {
        return 'Questo generatore permette di creare CRUD (Create, Read, Update, Delete) su model specifici';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return array_merge(parent::rules(), [
            [['formTabs'], 'filter', 'filter' => 'trim'],
            ['tabsFieldList', 'checkIsArray']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'formTabs' => 'Tabs',
        ]);
    }

    public function checkIsArray() {
        if (!is_array($this->tabsFieldList)) {
            $this->addError('tabsFieldList', 'tabsFieldList is not array!');
        }
    }

    /**
     * @inheritdoc
     */
    public function hints() {
        return array_merge(parent::hints(), [
            'formTabs' => 'Elenco delle tab da creare sulla form <code>tab1|tab2|...</code>.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates() {
        return ['controller.php'];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes() {
        return array_merge(parent::stickyAttributes(), []);
    }

    /**
     * Generates parameter tags for phpdoc
     * @return array parameter tags for phpdoc
     */
    public function generateActionParamComments() {
        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (($table = $this->getTableSchema()) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . (substr(strtolower($pk), -2) == 'id' ? 'integer' : 'string') . ' $' . $pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return ['@param ' . $table->columns[$pks[0]]->phpType . ' $id'];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . $table->columns[$pk]->phpType . ' $' . $pk;
            }

            return $params;
        }
    }

    /**
     * Returns table schema for current model class or false if it is not an active record
     * @return boolean|\yii\db\TableSchema
     */
    public function getTableSchema() {
        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema();
        } else {
            return false;
        }
    }

    /**
     * @return array model column names
     */
    public function getFormTabsAsArray() {
        $formTabsAsArray = [];
        if ($this->formTabs) {
            $formTabsAsArray = explode($this->formTabsSeparator, $this->formTabs);
        }

        if (!count($formTabsAsArray)) {
            $formTabsAsArray = ['dettagli'];
        }

        return $formTabsAsArray;
    }

    /**
     * @return array model column names
     */
    public function getAttributesTab($tabCode) {
        $attributes = [];
        if ($this->tabsFieldList && array_key_exists($tabCode, $this->tabsFieldList)) {
            if ($this->tabsFieldList[$tabCode]) {
                $attributes = explode($this->formTabsFieldSeparator, $this->tabsFieldList[$tabCode]);
            }
        } else {
            $attributes = $this->safeAttributes();
        }
        return $attributes;
    }

    public function generate() {
        try {
            $generator = parent::generate();

            $controllerFile = Yii::getAlias('@'.str_replace('\\', '/', ltrim($this->controllerClass, '\\')).'.php');
//            $translationFile = StringHelper::dirname($controllerFile).'/../i18n/it-IT/messages.php';
            $controllerClassName = \yii\helpers\StringHelper::basename($this->controllerClass);

//            $generatorFiles[] = new CodeFile($translationFile, $this->render('messages.php', [
//                'strings' => self::getTranslations($controllerClassName),
//                'controllerClassName' => $controllerClassName
//            ]));

            return $generator;
        } catch (Exception $ex) {
            return FALSE;
        }
    }

    /**
     * @param $module
     * @return array
     */
    public static function getTranslations($module) {
        $translationCacheName = 'mtc_' . $module;

        return (array) Yii::$app->cache->get($translationCacheName);
    }

    /**
     * @param $module
     * @param $slug
     * @param $text
     * @return bool
     */
    public static function addTranslation($module, $slug, $text) {
        $translationCacheName = 'mtc_' . $module;

        $moduleTranslations = Yii::$app->cache->get($translationCacheName);

        if(empty($moduleTranslations)) {
            $moduleTranslations = [];
        }

        $moduleTranslations[$slug] = $text;

        return true;
    }

}
