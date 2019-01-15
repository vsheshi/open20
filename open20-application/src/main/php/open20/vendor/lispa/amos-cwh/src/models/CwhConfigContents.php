<?php

namespace lispa\amos\cwh\models;

use cornernote\workflow\manager\models\Workflow;
use lispa\amos\core\record\Record;
use yii\helpers\ArrayHelper;

/**
 * Class CwhConfigContents
 * This is the model class for table "cwh_config_contents".
 *
 * @property array statuses
 *
 * @package lispa\amos\cwh\models
 */
class CwhConfigContents extends \lispa\amos\cwh\models\base\CwhConfigContents
{

    const WORKFLOW_AFFIX = 'Workflow';

    /**
     * @param $classname
     * @return static
     */
    public static function getConfig($classname)
    {
        return self::findOne(['classname' => $classname]);
    }

    /**
     * @return mixed
     */
    public static function getConfigs()
    {
        return self::find()->all();
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        $refClass = new \ReflectionClass($this->classname);

        $workflow = Workflow::findOne($refClass->getShortName() . self::WORKFLOW_AFFIX);
        $retArray = [];
        if (!is_null($workflow)) {
            $retArray = ArrayHelper::map($workflow->statuses, function ($array, $default) {
                return "{$array->workflow_id}/{$array->id}";
            }, 'label');
        }
        return $retArray;
    }

    /**
     * @return mixed
     */
    public function getModelAttributes()
    {

        $attributes = [];
        if(!empty($this->classname)) {
            if(class_exists($this->classname)){
                /** @var Record $modelObject */
                $modelObject = \Yii::createObject($this->classname);
                $modelAttributes = $modelObject->attributes();
                foreach ($modelAttributes as $attribute){
                    $attributes = ArrayHelper::merge($attributes, [$attribute => $attribute. ' (' . $modelObject->getAttributeLabel($attribute). ')']);
                }
            }
        }
        return $attributes;
    }

}
