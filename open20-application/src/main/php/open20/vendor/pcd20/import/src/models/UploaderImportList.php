<?php

namespace pcd20\import\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "uploader_import_list".
 */
class UploaderImportList extends \pcd20\import\models\base\UploaderImportList
{
    public function representingColumn()
    {
        return [
            //inserire il campo o i campi rappresentativi del modulo
        ];
    }

    public function attributeHints()
    {
        return [
        ];
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

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
        ]);
    }

    public function attributeLabels()
    {
        return
            ArrayHelper::merge(
                parent::attributeLabels(),
                [
                ]);
    }


    public static function getEditFields()
    {
        $labels = self::attributeLabels();

        return [
            [
                'slug' => 'name_file',
                'label' => $labels['name_file'],
                'type' => 'string'
            ],
            [
                'slug' => 'path_log',
                'label' => $labels['path_log'],
                'type' => 'string'
            ],
            [
                'slug' => 'successfull',
                'label' => $labels['successfull'],
                'type' => 'integer'
            ],
        ];
    }

    /**
     * @return string
     */
    public function getPathForLog()
    {
        $date = new \DateTime();
        $basePath = \Yii::getAlias('@backend/web/').'/reports_import/';
        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }
        return $basePath. $date->getTimestamp() . '.txt';
    }
}
