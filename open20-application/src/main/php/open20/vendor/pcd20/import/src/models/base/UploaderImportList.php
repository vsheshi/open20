<?php

namespace pcd20\import\models\base;

use Yii;

/**
 * This is the base-model class for table "uploader_import_list".
 *
 * @property integer $id
 * @property string $name_file
 * @property string $path_log
 * @property integer $successfull
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class UploaderImportList extends \lispa\amos\core\record\Record
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'uploader_import_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_file'], 'required'],
            [['successfull', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name_file', 'path_log'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('pcd20import', 'ID'),
            'name_file' => Yii::t('pcd20import', 'File'),
            'path_log' => Yii::t('pcd20import', 'File log'),
            'successfull' => Yii::t('pcd20import', 'Successo'),
            'created_at' => Yii::t('pcd20import', 'Creato il'),
            'updated_at' => Yii::t('pcd20import', 'Updated at'),
            'deleted_at' => Yii::t('pcd20import', 'Deleted at'),
            'created_by' => Yii::t('pcd20import', 'Created by'),
            'updated_by' => Yii::t('pcd20import', 'Updated at'),
            'deleted_by' => Yii::t('pcd20import', 'Deleted at'),
        ];
    }
}
