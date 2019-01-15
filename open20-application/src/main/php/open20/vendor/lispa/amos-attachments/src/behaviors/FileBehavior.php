<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\attachments
 * @category   CategoryName
 */

namespace lispa\amos\attachments\behaviors;

use lispa\amos\attachments\FileModule;
use lispa\amos\attachments\FileModuleTrait;
use lispa\amos\attachments\models\File;
use lispa\amos\core\views\toolbars\StatsToolbarPanels;
use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * Class FileBehavior
 * @property ActiveRecord $owner
 * @package file\behaviors
 */
class FileBehavior extends Behavior
{
    use FileModuleTrait;

    /**
     * @var array $permissions
     */
    public $permissions = [];

    /**
     * @var array $rules
     */
    var $rules = [];

    /**
     * @var array $fileValidators
     */
    private $fileValidators = [];

    /**
     * @var array $fileAttributes
     */
    private $fileAttributes = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        $events = [
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteUploads',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveUploads',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveUploads',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'evalAttributes'
        ];

        return $events;
    }

    /**
     * Iterate files and save by attribute
     * @param $event
     */
    public function saveUploads($event)
    {
        $attributes = $this->getFileAttributes();

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $this->saveAttributeUploads($attribute);
            }
        }
    }

    /**
     * Return array of attributes which may contain f
     * @return array
     */
    public function getFileAttributes()
    {
        $validators = $this->owner->getValidators();

        //Array of attributes
        $fileAttributes = [];

        //has file validator?
        $this->fileValidators = $this->getFileValidator($validators);

        foreach ($this->fileValidators as $fileValidator) {
            if (!empty($fileValidator)) {
                foreach ($fileValidator->attributes as $attribute) {
                    $fileAttributes[] = $attribute;
                }
            }
        }

        return $fileAttributes;
    }

    /**
     * Check if owner model has file validator
     * @param ArrayObject|\yii\validators\Validator[]
     * @return \yii\validators\Validator[]
     */
    public function getFileValidator($validators)
    {
        $fileValidators = [];

        foreach ($validators as $validator) {
            /** @var \yii\validators\Validator $validator */
            $classname = $validator::className();

            if ($classname == 'yii\validators\FileValidator') {
                $fileValidators[] = $validator;
            }
        }

        return $fileValidators;
    }

    protected function saveAttributeUploads($attribute)
    {
        $files = UploadedFile::getInstancesByName($attribute);

        if (!empty($files)) {
            foreach ($files as $file) {
                if (!$file->saveAs($this->getModule()->getUserDirPath($attribute) . $file->name)) {
                    continue;
                }
            }
        }

        if ($this->owner->isNewRecord) {
            return true;
        }

        $userTempDir = $this->getModule()->getUserDirPath($attribute);

        foreach (FileHelper::findFiles($userTempDir) as $file) {
            if (!$this->getModule()->attachFile($file, $this->owner, $attribute)) {
                $this->owner->addError($attribute, 'File upload failed.');
                return true;
            }
        }

        //Getting query
        $getter = 'get' . ucfirst($attribute);

        /** @var ActiveQuery $activeQuery */
        $getResult = $this->owner->{$getter}();
        if ($getResult instanceof ActiveQuery) {
            $this->owner->{$attribute} = $getResult->multiple ? $getResult->all() : $getResult->one();
        } else {
            $this->owner->{$attribute} = $getResult;
        }
    }

    /**
     * When update save files before the validation
     * @param $event
     * @return bool|void
     */
    public function evalAttributes($event)
    {
        $attributes = $this->getFileAttributes();

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $files = UploadedFile::getInstancesByName($attribute);
                if (!empty($files)) {
                    $maxFiles = 1;

                    foreach ($this->fileValidators as $fileValidator) {
                        if (!empty($fileValidator)) {
                            if (in_array($attribute, $fileValidator->attributes)) {
                                $maxFiles = $fileValidator->maxFiles;
                            }
                        }
                    }
                    $setter = 'set' . ucfirst($attribute);
                    if (method_exists($this->owner, $setter)) {
                        $this->owner->{$setter}($maxFiles == 1 ? reset($files) : $files);
                    } else {
                        if ($maxFiles != 1) {
                            $this->owner->{$attribute} = [];

                            foreach ($files as $file) {
                                $this->owner->{$attribute}[] = $file;
                            }
                        } else {
                            $this->owner->{$attribute} = reset($files);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $event
     */
    public function deleteUploads($event)
    {
        $files = $this->getFiles();

        foreach ($files as $file) {
            $this->getModule()->detachFile($file->id);
        }
    }

    /**
     * @param string $andWhere
     * @return File[]
     */
    public function getFiles($andWhere = '')
    {
        return $this->getFilesQuery()->all();
    }

    /**
     * @param string $andWhere
     * @return \yii\db\ActiveQuery
     */
    public function getFilesQuery($andWhere = '')
    {
        $fileQuery = File::find()
            ->where(
                [
                    'itemId' => $this->owner->getAttribute('id'),
                    'model' => $this->getModule()->getClass($this->owner)
                ]
            );
        $fileQuery->orderBy('is_main DESC, sort DESC');

        if ($andWhere) {
            $fileQuery->andWhere($andWhere);
        }

        return $fileQuery;
    }

    /**
     * DEPRECATED
     */
    public function getSingleFileByAttributeName($attribute = 'file', $sort = 'id')
    {
        $query = $this->getFilesByAttributeName($attribute, $sort);

        //Single result mode
        $query->multiple = false;

        return $this->hasOneFile($attribute, $sort);
    }

    /**
     * DEPRECATED
     */
    public function getFilesByAttributeName($attribute = 'file', $sort = 'id')
    {
        return $this->hasMultipleFiles($attribute, $sort);
    }

    /**
     * @param string $attribute
     * @param string $sort
     * @return \yii\db\ActiveQuery
     */
    public function hasMultipleFiles($attribute = 'file', $sort = 'id')
    {
        $fileQuery = File::find()
            ->where([
                'itemId' => $this->owner->id,
                'model' => $this->getModule()->getClass($this->owner),
                'attribute' => $attribute,
            ]);

        $fileQuery->orderBy([$sort => SORT_ASC]);

        //Single result mode
        $fileQuery->multiple = true;

        return $fileQuery;
    }

    /**
     * @param string $attribute
     * @param string $sort
     * @return \yii\db\ActiveQuery
     */
    public function hasOneFile($attribute = 'file', $sort = 'id')
    {
        $query = $this->hasMultipleFiles($attribute, $sort);

        //Single result mode
        $query->multiple = false;

        return $query;
    }

    /**
     * @param string $attribute
     * @return array
     */
    public function getInitialPreviewByAttributeName($attribute = 'file', $size = null)
    {
        $initialPreview = [];

        $userTempDir = $this->getModule()->getUserDirPath($attribute);
        foreach (FileHelper::findFiles($userTempDir) as $file) {

            if (substr(FileHelper::getMimeType($file), 0, 5) === 'image') {
                $initialPreview[] = Html::img(['/' . FileModule::getModuleName() . '/file/download-temp', 'filename' => basename($file)], ['class' => 'file-preview-image']);
            } else {
                $initialPreview[] = Html::beginTag('div', ['class' => 'file-preview-other']) .
                    Html::beginTag('span', ['class' => 'file-other-icon']) .
                    Html::tag('i', '', ['class' => 'glyphicon glyphicon-file']) .
                    Html::endTag('span') .
                    Html::endTag('div');
            }
        }

        $files = $this->getFilesByAttributeName($attribute)->all();

        foreach ($files as $file) {
            /** @var File $file */

            if (substr($file->mime, 0, 5) === 'image') {
                $initialPreview[] = Html::img($file->getUrl($size), ['class' => 'file-preview-image']);
            } else {
                $initialPreview[] = Html::beginTag('div', ['class' => 'file-preview-other']) .
                    Html::beginTag('span', ['class' => 'file-other-icon']) .
                    Html::tag('i', '', ['class' => 'glyphicon glyphicon-file']) .
                    Html::endTag('span') .
                    Html::endTag('div');
            }
        }

        return $initialPreview;
    }

    /**
     * @param string $attribute
     * @return array
     */
    public function getInitialPreviewConfigByAttributeName($attribute = 'file')
    {
        $initialPreviewConfig = [];

        $userTempDir = $this->getModule()->getUserDirPath($attribute);
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            $filename = basename($file);
            $initialPreviewConfig[] = [
                'caption' => $filename,
                'showDrag' => false,
                'indicatorNew' => false,
                'showRemove' => true,
                'size' => $file->size,
                'url' => Url::to(['/' . FileModule::getModuleName() . '/file/delete-temp',
                    'filename' => $filename
                ]),
            ];
        }

        $files = $this->getFilesByAttributeName($attribute)->all();

        if (is_array($files)) {
            foreach ($files as $index => $file) {
                $initialPreviewConfig[] = [
                    'caption' => "$file->name.$file->type",
                    'showDrag' => false,
                    'indicatorNew' => false,
                    'showRemove' => true,
                    'size' => $file->size,
                    'url' => Url::toRoute(['/' . FileModule::getModuleName() . '/file/delete',
                        'id' => $file->id,
                        'item_id' => $this->owner->getAttribute('id'),
                        'model' => $this->getModule()->getClass($this->owner),
                        'attribute' => $attribute
                    ])
                ];
            }
        }

        return $initialPreviewConfig;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getFileCount()
    {
        $count = File::find()
            ->where([
                'itemId' => $this->owner->getAttribute('id'),
                'model' => $this->getModule()->getClass($this->owner)
            ])
            ->count();
        return (int)$count;
    }

    /**
     * @return mixed
     */
    public function getStatsToolbar()
    {
        return StatsToolbarPanels::getDocumentsPanel($this->owner, $this->getFileCount());
    }
}
