<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\models
 * @category   CategoryName
 */

namespace lispa\amos\documenti\models;

use pendalf89\filemanager\behaviors\MediafileBehavior;
use pendalf89\filemanager\models\Mediafile;
use yii\helpers\ArrayHelper;
use lispa\amos\attachments\behaviors\FileBehavior;

/**
 * This is the model class for table "documenti_categorie".
 */
class DocumentiCategorie extends \lispa\amos\documenti\models\base\DocumentiCategorie
{

    /**
     * @var mixed $file File.
     */
    public $file;

    /**
     * @var $documentMainFile
     */
    public $documentCategoryImage;

    /**
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['documentCategoryImage'], 'file', 'extensions' => 'jpeg, jpg, png, gif','maxFiles' => 1],
        ]);
    }

    /**
     * Ritorna l'url dell'avatar.
     *
     * @param string $dimension Dimensione. Default = small.
     * @return string Ritorna l'url.
     */
    public function getAvatarUrl($dimension = 'small')
    {
        $url = '/img/img_default.jpg';
        if ($this->filemanager_mediafile_id) {
            $mediafile = Mediafile::findOne($this->filemanager_mediafile_id);
            if ($mediafile) {
                $url = $mediafile->getThumbUrl($dimension);
            }
        }
        return $url;
    }

    /**
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ],
        ]);
    }

    /**
     *
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->documentCategoryImage = $this->getDocumentCategoryImage()->one();
    }

    /**
     * Getter for $this->documentCategoryImage;
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentCategoryImage()
    {
        return $this->hasOneFile('documentCategoryImage');
    }

}
