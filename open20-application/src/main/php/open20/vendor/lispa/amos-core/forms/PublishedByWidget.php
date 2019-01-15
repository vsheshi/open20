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

use lispa\amos\core\helpers\Html;
use lispa\amos\core\module\BaseAmosModule;
use Yii;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class PublishedByWidget
 *
 * shows the entities name publishing the content and the selected publication rule
 *
 * The layout determines how different sections of the list view should be organized.
 * The following tokens will be replaced with the corresponding section contents:
 *
 * - `{publisher}`: publisher user. See [[renderPublisher()]].
 * - `{publisherAdv}`: publisher user info with validator info. See [[renderPublisherAdv()]].
 * - `{publishingRules}`: publisher user info with validator info. See [[renderPublisherRules()]].
 * - `{target}`: the target. See [[renderTarget()]].
 * - `{targetAdv}`: the target. See [[renderTargetAdv()]].
 * - `{category}`: the category of current model. See [[renderCategory()]].
 * - `{status}`: the status of current model. See [[renderStatus()]].
 * - `{pubblicationdates}`: the publication date begin and end of current model. See [[renderPubblicationDates()]].
 * - `{pubblishedfrom}`: the publication date begin of current model. See [[renderPublishedFrom()]].
 * - `{pubblishedat}`: the publication date end of current model. See [[renderPublishedAt()]].
 *
 * @package lispa\amos\core\forms
 */
class PublishedByWidget extends Widget
{
    /**
     * @var string the layout that determines how different sections of the list view should be organized.
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{publisher}`: publisher user. See [[renderPublisher()]].
     * - `{publisherAdv}`: publisher user info with validator info. See [[renderPublisherAdv()]].
     * - `{publishingRules}`: publisher user info with validator info. See [[renderPublisherRules()]].
     * - `{target}`: the target. See [[renderTarget()]].
     * - `{targetAdv}`: the target. See [[renderTargetAdv()]].
     * - `{category}`: the category of current model. See [[renderCategory()]].
     * - `{status}`: the status of current model. See [[renderStatus()]].
     * - `{pubblicationdates}`: the publication date begin and end of current model. See [[renderPubblicationDates()]].
     * - `{pubblishedfrom}`: the publication date begin of current model. See [[renderPublishedFrom()]].
     * - `{pubblishedat}`: the publication date end of current model. See [[renderPublishedAt()]].
     *
     */
    //public $layout = "{publisher}\n{publishingRules}\n{publishingAdv}\n{targetAdv}\n{category}\n{status}";
    public $layout = "{targetAdv}";

    /**
     * @var Model the current model processed
     */
    public $model = null;

    /**
     * @var Model Context
     */
    public $modelContext = null;

    /**
     * @var array $renderSections
     */
    public $renderSections = [];

    /**
     * @var array the HTML attributes for the container tag of the list view.
     * The "tag" element specifies the tag name of the container element and defaults to "div".
     */
    public $options = [
        'class' => 'published-by'
    ];

    /**
     * @var string $emptyText
     */
    public $emptyText = null;

    /**
     * @var string $separatorText
     */
    public $separatorText;

    /**
     * @var string $sectionTag
     */
    public $sectionTag = 'div';

    /**
     * @var string $sectionOptions
     */
    public $sectionOptions = [
        'class' => 'item'
    ];


    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        if ($this->emptyText === null) {
            $this->emptyText = " - ";
        }

        if ($this->separatorText === null) {
            $this->separatorText = ", ";
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        if (NULL != (\Yii::$app->getModule('cwh'))) {
            $content = preg_replace_callback("/{\\w+}/", function ($matches) {
                $content = $this->renderSection($matches[0]);

                return $content === false ? $matches[0] : $content;
            }, $this->layout);
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'div');
            echo Html::tag($tag, $content, $options);
        }
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{publisher}`, `{publisherAdv}`.
     * @return string|bool the rendering result of the section, or false if the named section is not supported.
     * @throws \yii\base\InvalidConfigException
     */
    public function renderSection($name)
    {
        if (isset($this->renderSections[$name]) && $this->renderSections[$name] instanceof \Closure) {
            return call_user_func($this->renderSections[$name], $this->model, $this);
        }
        switch ($name) {
            case '{publisher}':
                return $this->renderPublisher();
            case '{publisherAdv}':
                return $this->renderPublisherAdv();
            case '{publishingRules}':
                return $this->renderPublishingRules();
            case '{target}':
                return $this->renderTarget();
            case '{targetAdv}':
                return $this->renderTargetAdv();
            case '{category}':
                return $this->renderCategory();
            case '{status}':
                return $this->renderStatus();
            case '{pubblicationdates}' :
                return $this->renderPubblicationDates();
            case '{pubblishedfrom}' :
                return $this->renderPublishedFrom();
            case '{pubblishedat}' :
                return $this->renderPublishedAt();
            case '{createdat}' :
                return $this->renderCreatedAt();
            default:
                return false;
        }
    }

    /**
     * @param bool $onlyContent
     * @return string
     */
     public function renderPublisher($onlyContent = false)
    {
        $userProfile = $this->model->createdUserProfile;
        if (!isset($userProfile)) {
            return '';
        }

        if (!empty($this->modelContext) && $this->modelContext->hasProperty('publicatedByLabel') && (strlen($this->modelContext->publicatedByLabel) > 0)) {
            $content = $this->modelContext->publicatedByLabel .": ".$userProfile->nomeCognome;
        }
        elseif ($this->model->hasProperty('publicatedByLabel') && (strlen($this->model->publicatedByLabel) > 0)) {
            $content = $this->model->publicatedByLabel .": ".$userProfile->nomeCognome;
        }
        else {
            $content = BaseAmosModule::t("amoscore", "<label>Creato da</label>: {content}", [
                'content' => $userProfile->nomeCognome
            ]);
        }

        if ($onlyContent) {
            return $content;
        }

        return Html::tag($this->sectionTag, $content, $this->sectionOptions);
    }

    /**
     * @return string the rendering result
     * @throws \yii\base\InvalidConfigException
     */
    public function renderPublisherAdv()
    {
        $content = $this->renderPublisher(true);
        $targets = '';

        try {
            $targets = $this->model->getTargets();
        } catch (\Exception $exception) {
            if ($this->model->hasProperty('validatori')) {
                $targets = $this->model->validatori;
            }
        }

        if ($targets) {
            $targetsCollection = \lispa\amos\cwh\models\CwhNodi::findAll([
                'id' => $targets
            ]);

            $targetArr = [];
            foreach ($targetsCollection as $target) {
                $targetArr[] = $this->findNode($target)->__toString();
            }
            $content .= (!empty($targetArr) ? ' - ' : '') . implode($this->separatorText, $targetArr);
        }

        return Html::tag($this->sectionTag, $content, $this->sectionOptions);
    }

    /**
     * @param array $Target
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    protected function findNode($Target)
    {
        $modelClass = \Yii::createObject($Target['classname']);
        $model = $modelClass->findOne($Target['record_id']);
        return $model;
    }

    /**
     * @return string the rendering result
     */
    public function renderPublishingRules()
    {
        $content = \lispa\amos\cwh\utility\CwhUtil::getPublicationRuleLabel($this->model->regola_pubblicazione);
        return Html::tag($this->sectionTag, BaseAmosModule::t("amoscore", "<label>Regola di pubblicazione</label> : {el}", [
            'el' => $content
        ]), $this->sectionOptions);
    }

    /**
     * @return string the rendering result
     * @throws \yii\base\InvalidConfigException
     */
    public function renderTarget()
    {
        $content = '';
        $targets = '';

        try {
            $targets = $this->model->getTargets();
        } catch (\Exception $exception) {
            if ($this->model->hasProperty('destinatari')) {
                $targets = $this->model->destinatari;
            }
        }

        if ($targets) {
            $content = $this->getNodesAsString($targets);
        } else {
            $content .= $this->emptyText;
        }

        return Html::tag($this->sectionTag, BaseAmosModule::t("amoscore", "<label>Destinatari</label> : {el}", [
            'el' => $content
        ]), $this->sectionOptions);
    }

    /**
     * @param $nodes
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getNodesAsString($nodes)
    {
        $targetsCollection = \lispa\amos\cwh\models\CwhNodi::findAll([
            'id' => $nodes
        ]);

        $targetArr = [];
        foreach ($targetsCollection as $target) {
            $targetArr[] = $this->findNode($target)->__toString();
        }

        return implode($this->separatorText, $targetArr);
    }

    /**
     * @return string the rendering result
     * @throws \yii\base\InvalidConfigException
     */
    public function renderTargetAdv()
    {
        $targets = '';
        try {
            $targets = $this->model->getTargets();
        } catch (\Exception $exception) {
            if ($this->model->hasProperty('destinatari')) {
                $targets = $this->model->destinatari;
            }
        }
        $publicationRule = \lispa\amos\cwh\utility\CwhUtil::getPublicationRuleLabel($this->model->regola_pubblicazione);
        $targetsString = $this->getNodesAsString($targets);
        if (count($targets)) {
            $content = BaseAmosModule::t("amoscore", "<label>Destinatari</label>: <span class='rules'>{rule}</span> in <span class='target'>{target}</span>", [
                'rule' => $publicationRule,
                'target' => $targetsString
            ]);
        } else {
            $content = BaseAmosModule::t("amoscore", "<label>Destinatari</label>: <span class='rules'>{rule}</span>", [
                'rule' => $publicationRule
            ]);
        }

        return Html::tag($this->sectionTag, $content, $this->sectionOptions);
    }

    /**
     * @return string the rendering result
     */
    public function renderCategory()
    {
        $retValue = '';
        $description = '';

        try {
            if ($this->model->hasProperty('category')) {
                $category = $this->model->category;
                if ($category) {
                    $description = $category->titolo;
                }
            }
            $retValue = Html::tag($this->sectionTag, BaseAmosModule::t("amoscore", "<label>Categoria</label>: {el}", [
                'el' => $description
            ]), $this->sectionOptions);

        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        return $retValue;
    }

    /**
     * @return string the rendering result
     */
    public function renderStatus()
    {
        $retValue = '';
        $status = '';

        try {
            if ($this->model->hasMethod('hasWorkflowStatus')) {
                if ($this->model->hasWorkflowStatus()) {
                    $status = $this->model->getWorkflowStatus()->label;
                }
            }
            $content = Html::tag('label', BaseAmosModule::t('amoscore', 'Status')) . ': ';
            if ($this->model->hasProperty('translatedStatus')) {
                $content .= $this->model->translatedStatus;
            } else {
                $content .= $status;
            }
            $retValue = Html::tag($this->sectionTag, $content, $this->sectionOptions);

        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        return $retValue;
    }

    /**
     * This method render the publication dates
     * @return string
     */
    public function renderPubblicationDates()
    {
        $retValue = $this->renderPublishedFrom() . $this->renderPublishedAt();
        return $retValue;
    }

    /**
     * This method render the published from date.
     * @return string
     */
    public function renderPublishedFrom()
    {
        try {
            // Date from
            $dateFrom = '';
            if ($this->model->hasProperty('publicatedFrom')) {
                $dateFrom = \Yii::$app->getFormatter()->asDate($this->model->publicatedFrom, 'long');
            }
            $fromContent = BaseAmosModule::t("amoscore", "<label>Publicated from</label> : {el}", [
                'el' => $dateFrom
            ]);
            if ($this->model->hasProperty('publicatedFromLabel') && (strlen($this->model->publicatedFromLabel) > 0)) {
                $fromContent = Html::tag('label', $this->model->publicatedFromLabel) . ': ' . $dateFrom;
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        // Retval
        $retValue = Html::tag($this->sectionTag, $fromContent, $this->sectionOptions);

        return $retValue;
    }

    /**
     * This method render the published at date.
     * @return string
     */
    public function renderPublishedAt()
    {
        try {
            // Date to
            $dateTo = '';
            if ($this->model->hasProperty('publicatedAt')) {
                $dateTo = \Yii::$app->getFormatter()->asDate($this->model->publicatedAt, 'long');
            }
            $toContent = Html::label(BaseAmosModule::t('amoscore', '#publicated_at_published_by_widget')) . ': ' . $dateTo;
            if ($this->model->hasProperty('publicatedAtLabel') && (strlen($this->model->publicatedAtLabel) > 0)) {
                $toContent = Html::tag('label', $this->model->publicatedAtLabel) . ': ' . $dateTo;
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        // Retval
        $retValue = Html::tag($this->sectionTag, $toContent, $this->sectionOptions);

        return $retValue;
    }


    /**
     * This method render the created at date.
     * @return string
     */
    public function renderCreatedAt()
    {
        try {
            // Date to
            $dateTo = '';
            if ($this->model->hasProperty('created_at')) {
                $dateTo = \Yii::$app->getFormatter()->asDate($this->model->created_at, 'long');
            }
            $toContent = Html::tag('label', Html::label(BaseAmosModule::t('amoscore', '#created_at'))) . ': ' . $dateTo;

        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        // Retval
        $retValue = Html::tag($this->sectionTag, $toContent, $this->sectionOptions);

        return $retValue;
    }
}
