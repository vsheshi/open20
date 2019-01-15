<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\record
 * @category   CategoryName
 */

namespace lispa\amos\core\record;

use lispa\amos\core\behaviors\BlameableBehavior;
use lispa\amos\core\behaviors\EJsonBehavior;
use lispa\amos\core\behaviors\SoftDeleteByBehavior;
use lispa\amos\core\behaviors\VersionableBehaviour;
use lispa\amos\core\interfaces\StatsToolbarInterface;
use lispa\amos\core\interfaces\WorkflowModelInterface;
use lispa\amos\core\module\AmosModule;
use Yii;
use yii\base\Behavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\helpers\Inflector;
use yii\web\Application as Web;
use function in_array;

/**
 * Class Record
 *
 * @property \lispa\amos\admin\models\UserProfile $createdUserProfile
 * @property \lispa\amos\admin\models\UserProfile $updatedUserProfile
 * @property \lispa\amos\admin\models\UserProfile $deletedUserProfile
 *
 * @package lispa\amos\core\record
 */
class Record extends ActiveRecord implements StatsToolbarInterface
{
    const SCENARIO_FAKE_REQUIRED = 'scenario_fake_required';

    public static $modulesChainBehavior = [];

    /**
     * @var array Array of order fields get from the config file of the module
     */
    public $orderAttributes = NULL;

    /**
     * @var string Selected ORDER attribute (field) from the ORDER form
     */
    public $orderAttribute = NULL;

    /**
     * @var integer ORDER ascending (SORT_ASC), descending (SORT_DESC)
     */
    public $orderType = NULL;

    protected $adminInstalled = NULL;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_FAKE_REQUIRED] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    /**
     * Base query, it exclude deleted elements
     *
     * @return mixed
     */
    public static function find()
    {
        $return = parent::find();
        if (array_key_exists('deleted_at', static::getTableSchema()->columns)) {
            $tableName = static::getTableSchema()->name;
            $return->andWhere([$tableName . '.deleted_at' => null]);
        }
        return $return;
    }

    /**
     * Array of fields => labels for the ORDER form
     * see "_order.php" file
     * @return mixed
     */
    public function getOrderAttributesLabels()
    {
        $labels = [];
        if ($this->orderAttributes) {
            foreach ($this->orderAttributes as $value) {
                $labels[$value] = $this->getAttributeLabel($value);
            }
        }
        return $labels;
    }

    /**
     *Init the order variables from the module config
     */
    public function initOrderVars()
    {
        //if the search is enabled
        if (
            isset(\Yii::$app->controller->module)
            &&
            isset(\Yii::$app->controller->module->params['orderParams'][\Yii::$app->controller->id])
        ) {
            //clean var
            $moduleParams = \Yii::$app->controller->module->params['orderParams'][\Yii::$app->controller->id];

            //check if is set an array of order params
            if (
                isset($moduleParams['fields'])
                &&
                $moduleParams['fields']
            ) {
                $this->setOrderAttributes($moduleParams['fields']);
            }

            //check if is set a default value
            if (
                isset($moduleParams['default_field'])
                &&
                $moduleParams['default_field']
            ) {
                $this->setOrderAttribute($moduleParams['default_field']);
            }

            //check if is set a default order value
            if (
                isset($moduleParams['order_type'])
                &&
                $moduleParams['order_type']
            ) {
                $this->setOrderType($moduleParams['order_type']);
            }
        }
    }

    /**
     * Set the list of fields order for this module
     *
     * @param array $fields
     * @return bool
     */
    public function setOrderAttributes($fields = ['id'])
    {
        $this->orderAttributes = $fields;
        return true;
    }

    /**
     * Set order field
     *
     * @param string $field
     * @return bool
     */
    public function setOrderAttribute($field = 'id')
    {
        if ($this->orderAttributes && in_array($field, $this->orderAttributes)) {
            $this->orderAttribute = $field;
        } else {
            $this->orderAttribute = 'id';
        }
        return true;
    }

    /**
     * Set order type: ascending (SORT_ASC), descending (SORT_DESC)
     * @param int $type
     * @return bool
     */
    public function setOrderType($type = SORT_ASC)
    {
        $this->orderType = (int)$type;
        return true;
    }

    /**
     * Identifies the sort fields
     *
     * @param $params
     */
    public function setOrderVars($params)
    {
        $classSearch = Inflector::id2camel(\Yii::$app->controller->id, '-') . 'Search';

        if (
            array_key_exists($classSearch, $params)
            &&
            array_key_exists("orderAttribute", $params[$classSearch])
            &&
            array_key_exists("orderType", $params[$classSearch])
        ) {
            $this->setOrderAttribute($params[$classSearch]["orderAttribute"]);
            $this->setOrderType($params[$classSearch]["orderType"]);
        }
    }

    /**
     * Check if there is an order variable for the module
     *
     * @return bool
     */
    public function canUseModuleOrder()
    {
        return ($this->orderAttribute && $this->orderType);
    }

    public function init()
    {
        parent::init();
        $this->adminInstalled = \Yii::$app->getModule('admin');
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(),
            [
                'orderAttribute' => AmosModule::t('amoscore', 'Campo di ordinamento'),
                'orderType' => \Yii::t('amoscore', 'Criterio di ordinamento'),
                'createdUserProfile' => \Yii::t('amoscore', 'Creato da'),
                'updatedUserProfile' => \Yii::t('amoscore', 'Ultimo aggiornamento di'),
                'deletedUserProfile' => \Yii::t('amoscore', 'Cancellato da')
            ]
        );
    }

    public function behaviors()
    {
        $behaviorsParent = parent::behaviors();

        $behaviors = [
            "EJsonBehavior" => [
                'class' => EJsonBehavior::className()
            ],
            "SoftDeleteByBehavior" => [
                'class' => SoftDeleteByBehavior::className()
            ],
            "TimestampBehavior" => [
                'class' => TimestampBehavior::className(),
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
            "BlameableBehavior" => [
                'class' => BlameableBehavior::className(),
            ],
            "VersionableBehaviour" => [
                'class' => VersionableBehaviour::className(),
                'versionTable' => "{$this->tableName()}_version"
            ],
        ];

        // ciclo che innesta i behaviors nei model destinati
        foreach (self::$modulesChainBehavior as $item) {
            $module = \Yii::$app->getModule($item);
            if (isset($module) && in_array(self::className(), $module->modelsEnabled) && $module->behaviors) {
                $behaviors = ArrayHelper::merge($module->behaviors, $behaviors);
            }
        }

        return ArrayHelper::merge($behaviorsParent, $behaviors);
    }

    public function __toString()
    {
        $representingColumn = $this->representingColumn();
        if (($representingColumn === null) || ($representingColumn === array()))
            if ($this->getTableSchema()->primaryKey !== null) {
                $representingColumn = $this->getTableSchema()->primaryKey;
            } else {
                $columnNames = $this->getTableSchema()->getColumnNames();
                $representingColumn = $columnNames[0];
            }

        if (is_array($representingColumn)) {
            $part = '';
            foreach ($representingColumn as $representingColumn_item) {
                $part .= ($this->$representingColumn_item === null ? '' : $this->__shortText($this->$representingColumn_item, 30)) . ' ';
            }
            return substr($part, 0, -1);
        } elseif (is_string($representingColumn)) {
            return $representingColumn;
        } else {
            return $this->$representingColumn === null ? '' : (string)$this->$representingColumn;
        }
    }

    public function representingColumn()
    {
        return null;
    }

    /**
     * Parse string and return limited one
     * @param $text
     * @param $char_limit
     * @return string
     */
    protected function __shortText($text, $char_limit)
    {
        //Remove html tags
        $asString = strip_tags($text);

        //If already good string
        if (strlen($asString) < $char_limit) {
            return $asString;
        }

        //Limit string
        $asString = substr($asString, 0, $char_limit + 1);

        //Explode to array
        $arrayString = explode(' ', $asString);

        if (count($arrayString) > 1) {
            //Remove last word
            array_pop($arrayString);

            //Merge string
            $asString = implode(' ', $arrayString);
        }

        //Return it
        return $asString . "...";
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUserProfile()
    {
        if ($this->adminInstalled) {
            $modelClass = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile');
            return $this->hasOne($modelClass::className(), ['user_id' => 'created_by']);
        } else {
            return null;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUserProfile()
    {
        if ($this->adminInstalled) {
            $modelClass = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile');
            return $this->hasOne($modelClass::className(), ['user_id' => 'updated_by']);
        } else {
            return null;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedUserProfile()
    {
        if ($this->adminInstalled) {
            $modelClass = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile');
            return $this->hasOne($modelClass::className(), ['user_id' => 'deleted_by']);
        } else {
            return null;
        }
    }

    /**
     * Override for demos
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function beforeSave($insert)
    {
        $isDemo = $this->isDemo();

        if ($isDemo && (Yii::$app instanceof Web) && $this->inBlackList()) {
            \Yii::$app->session->addFlash('success', \Yii::t('amoscore', 'In Demo non &eacute; possibile modificare i contenuti'));
            return false;
        }

        return parent::beforeSave($insert);
    }

    /**
     * Check is demo environment
     * @return bool
     */
    public function isDemo()
    {
        $demoVar = isset(\Yii::$app->params['isDemo']) ? \Yii::$app->params['isDemo'] : false;
        return $demoVar ?: false;
    }


    /**
     *
     */
    private function inBlackList()
    {
        $ret = false;
        $demoModelBlackList = isset(\Yii::$app->params['demoModelBlackList']) ? \Yii::$app->params['demoModelBlackList'] : [];
        foreach ($demoModelBlackList as $cls) {
            if ($this instanceof $cls) {
                $ret = true;
                break;
            }
        }
        return $ret;
    }

    /**
     * Override for demos
     * @return bool
     */
    public function beforeDelete()
    {
        $isDemo = $this->isDemo();

        if ($isDemo && (Yii::$app instanceof Web)) {
            \Yii::$app->session->addFlash('success', \Yii::t('amoscore', 'In Demo non &eacute; possibile modificare i contenuti'));
            return false;
        }

        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (isset(Yii::$app->params['forms-purify-data']) && (Yii::$app->params['forms-purify-data'] == true)) {
            if (isset(Yii::$app->params['forms-purify-data'])) {
                $listClassModels = Yii::$app->params['forms-purify-data-white-models'];
                if (in_array($this->className(), $listClassModels)) {
                    return parent::beforeValidate();
                }
            }

            $listAttributes = $this->attributes;
            foreach ($listAttributes as $key => $attribute) {
                $this->$key = HtmlPurifier::process($this->$key);
            }

        }
        return parent::beforeValidate();
    }

    /**
     * method return user ids of record validators
     * @return array
     */
    public function getValidatorUsersId()
    {
        $users = [];

        try {
            $moduleCwh = Yii::$app->getModule('cwh');
            if (isset($moduleCwh) && in_array($this->className(), $moduleCwh->modelsEnabled)) {
                $users = \lispa\amos\cwh\models\CwhAuthAssignment::find()->andWhere([
                    'item_name' => $moduleCwh->permissionPrefix . '_VALIDATE_' . $this->className(),
                ])->andWhere(['in', 'cwh_nodi_id', $this->validatori])
                    ->select('user_id')->groupBy('user_id')->asArray()->column();
            }
            if (empty($users) && $this instanceof WorkflowModelInterface) {
                $validatorRole = $this->getValidatorRole();
                $authManager = \Yii::$app->authManager;
                $users = $authManager->getUserIdsByRole($validatorRole);
            }

        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $users;
    }

    /**
     * @inheritdoc
     */
    public function getStatsToolbar()
    {
        $panels = [];

        $behaviors = $this->getBehaviors();
        /**@var $behavior Behavior */
        foreach ($behaviors as $behavior) {
            if ($behavior->hasMethod(__FUNCTION__)) {
                $panelsAttributes = $behavior->{__FUNCTION__}();
                $panels = ArrayHelper::merge($panels, $panelsAttributes);
            }
        }
        return $panels;
    }

    /**
     * This method detach a behavior from the model.
     * @param string $className
     */
    public function detachBehaviorByClassName($className)
    {
        $behaviors = $this->getBehaviors();
        foreach ($behaviors as $index => $behavior) {
            /** @var Behavior $behavior */
            if ($behavior->className() == $className) {
                $this->detachBehavior($index);
            }
        }
    }

    /**
     * @param array $whiteList
     */
    public function detachBehaviorsOnWhiteList(array $whiteList)
    {
        $behaviors = $this->getBehaviors();
        foreach ($behaviors as $index => $behavior) {
            /** @var Behavior $behavior */
            if (!in_array($index, $whiteList)) {
                $this->detachBehavior($index);
            }
        }
    }

    /**
     * This method return an array of array. The array keys are all the model fields
     * and the values are arrays with "name! and "id" keys modified with the string
     * contained in the param. The return array structure is the following:
     * $newNameAndIds = [
     *  'FIELD_NAME_1' => [
     *      'name' => 'NEW_NAME',
     *      'id' => 'NEW_ID'
     *  ],
     *  .
     *  .
     *  .
     * ];
     * @param string $formNameSuffix
     * @return array
     */
    public function renameFormNamesAndIds($formNameSuffix)
    {
        $newFormFieldNamesAndIds = [];
        foreach ($this->attributes() as $attribute) {
            $newFormFieldNamesAndIds[$attribute] = [
                'name' => $this->formName() . $formNameSuffix . '[' . $attribute . ']',
                'id' => strtolower($this->formName()) . '-' . strtolower($formNameSuffix) . '-' . $attribute,
            ];
        }
        return $newFormFieldNamesAndIds;
    }
}
