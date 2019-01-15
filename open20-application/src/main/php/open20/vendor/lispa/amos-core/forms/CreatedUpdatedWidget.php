<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\forms
 * @category   CategoryName
 */

namespace lispa\amos\core\forms;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\module\BaseAmosModule;
use yii\base\Widget;
use yii\db\ActiveRecord;

/**
 * Class CreatedUpdatedWidget
 *
 * The widget requires only one parameter: the model
 *
 * @package lispa\amos\core\forms
 */
class CreatedUpdatedWidget extends Widget
{
    const CREATED_TYPE = 'created';
    const UPDATED_TYPE = 'updated';
    
    /**
     * @var string $layout Widget layout
     */
    public $layout = "{beginContainerSection}\n{createdSection}\n{updatedSection}\n{endContainerSection}";
    
    /**
     * @var array $containerOptions Default to []
     */
    public $containerOptions = [];
    
    /**
     * @var array $createdSectionOptions Default to []
     */
    public $createdSectionOptions = [];
    
    /**
     * @var array $updatedSectionOptions Default to []
     */
    public $updatedSectionOptions = [];
    
    /**
     * @var \lispa\amos\core\record\Record $model
     */
    private $model = null;
    
    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();
        
        if (is_null($this->model)) {
            throw new \Exception('CreatedUpdatedWidget: model required');
        }
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->model->isNewRecord) {
            return '';
        }
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);
            return $content === false ? $matches[0] : $content;
        }, $this->layout);
        return $content;
    }
    
    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|boolean the rendering result of the section, or false if the named section is not supported.
     */
    protected function renderSection($name)
    {
        switch ($name) {
            case '{beginContainerSection}':
                return $this->renderBeginContainerSection();
            case '{createdSection}':
                return $this->renderCreatedSection();
            case '{updatedSection}':
                return $this->renderUpdatedSection();
            case '{endContainerSection}':
                return $this->renderEndContainerSection();
            default:
                return false;
        }
    }
    
    /**
     * This method render the beginning part of the container.
     * @return string
     */
    protected function renderBeginContainerSection()
    {
        $sectionContent = Html::beginTag('div', $this->containerOptions);
        return $sectionContent;
    }
    
    /**
     * This method render the created section.
     * @return string
     */
    protected function renderCreatedSection()
    {
        $createdAt = $this->getModel()->created_at;
        $sectionContent = Html::beginTag('p', $this->createdSectionOptions);
        $sectionContent .= Html::beginTag('strong');
        $sectionContent .= BaseAmosModule::tHtml('amoscore', 'Creazione') . ': ';
        $sectionContent .= Html::endTag('strong');
        $sectionContent .= BaseAmosModule::tHtml('amoscore', 'da') . ' ' . $this->retrieveUserNameAndSurname(self::CREATED_TYPE) . ' ';
        $sectionContent .= BaseAmosModule::tHtml('amoscore', 'il') . ' ' . \Yii::$app->getFormatter()->asDate($createdAt) . ' ';
        $sectionContent .= BaseAmosModule::tHtml('amoscore', 'alle ore') . ' ' . \Yii::$app->getFormatter()->asTime($createdAt);
        $sectionContent .= Html::endTag('p');
        return $sectionContent;
    }
    
    /**
     * This method render the updated section.
     * @return string
     */
    protected function renderUpdatedSection()
    {
        $updatedAt = $this->getModel()->updated_at;
        $sectionContent = Html::beginTag('p', $this->updatedSectionOptions);
        $sectionContent .= Html::beginTag('strong');
        $sectionContent .= BaseAmosModule::tHtml('amoscore', 'Ultima modifica') . ': ';
        $sectionContent .= Html::endTag('strong');
        $sectionContent .= BaseAmosModule::tHtml('amoscore', 'da') . ' ' . $this->retrieveUserNameAndSurname(self::UPDATED_TYPE) . ' ';
        $sectionContent .= BaseAmosModule::tHtml('amoscore', 'il') . ' ' . \Yii::$app->getFormatter()->asDate($updatedAt) . ' ';
        $sectionContent .= BaseAmosModule::tHtml('amoscore', 'alle ore') . ' ' . \Yii::$app->getFormatter()->asTime($updatedAt);
        $sectionContent .= Html::endTag('p');
        return $sectionContent;
    }
    
    /**
     * This method render the end part of the container.
     * @return string
     */
    protected function renderEndContainerSection()
    {
        $sectionContent = Html::endTag('div');
        return $sectionContent;
    }
    
    /**
     * This method creates a string that contains the name and surname of the user whose ID is contained in the parameter.
     * @param string $type Field of the model that contains a user id (eg. created_by, updated_by, ...)
     * @return string
     */
    private function retrieveUserNameAndSurname($type = '')
    {
        /** @var UserProfile $userProfile */
        $userProfile = null;
        switch ($type) {
            case self::CREATED_TYPE:
                $userProfile = $this->model->getCreatedUserProfile()->one();
                break;
            case self::UPDATED_TYPE:
                $userProfile = $this->model->getUpdatedUserProfile()->one();
                break;
        }
        $nameSurname = (!is_null($userProfile) ? $userProfile->getNomeCognome() : '-');
        return $nameSurname;
    }
    
    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    /**
     * @return ActiveRecord
     */
    public function getModel()
    {
        return $this->model;
    }
    
    /**
     * @param ActiveRecord $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }
}
