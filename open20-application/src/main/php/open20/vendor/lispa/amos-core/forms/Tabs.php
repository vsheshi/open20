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
use lispa\amos\core\controllers\BaseController;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\tag\AmosTag;
use yii\base\InvalidConfigException;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Tabs as BaseTabs;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class Tabs
 * @package lispa\amos\core\forms
 */
class Tabs extends BaseTabs
{
    /**
     * @var bool $hideTagsTab If true hide the tags tab.
     */
    public $hideTagsTab = false;

    /**
     * @var bool $hideCwhTab If true hide the cwh tab.
     */
    public $hideCwhTab = false;

    /**
     * @var bool $hideReportTab If true hide the report tab.
     */
    public $hideReportTab = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $moduleL = \Yii::$app->getModule('layout');
        if (!empty($moduleL)) {
            \lispa\amos\layout\assets\TabsAsset::register($this->getView());
        } else {
            \lispa\amos\core\views\assets\TabsAsset::register($this->getView());
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function renderItems()
    {
        $headers = [];
        $panes   = [];
        $model   = null;

        if (!$this->hasActiveTab() && !empty($this->items)) {
            $this->items[0]['active'] = true;
        }

        $content = '';

        if (\Yii::$app->controller instanceof BaseController) {
            $model = \Yii::$app->controller->model;
        }
        if (count(\yii\base\Widget::$stack) && isset($model)) {
            /*             * @var \lispa\amos\cwh\AmosCwh $moduleCwh */
            $moduleCwh  = \Yii::$app->getModule('cwh');
            $contentCwh = '';
            if (isset($moduleCwh) && in_array(get_class($model), $moduleCwh->modelsEnabled) && $moduleCwh->behaviors && !$this->hideCwhTab) {
                $contentCwh = \Yii::$app->controller->renderFile('@vendor/lispa/amos-cwh/src/views/pubblicazione/cwh.php',
                    [
                    'model' => $model,
                    'form' => \yii\base\Widget::$stack[0]
                ]);
                $content    .= $contentCwh;
            }

            /*             * @var AmosTag $moduleTag */
            $moduleTag = \Yii::$app->getModule('tag');
            if (isset($moduleTag) && in_array(get_class($model), $moduleTag->modelsEnabled) && $moduleTag->behaviors && !$this->hideTagsTab) {
                $contentTag = \lispa\amos\tag\widgets\TagWidget::widget([
                        'model' => $model,
                        'attribute' => 'tagValues',
                        'form' => \yii\base\Widget::$stack[0]
                ]);
                $content    .= $contentTag;
            }

            if (strlen($content) && !($model instanceof UserProfile) && !$this->hideTagsTab) {
                if (empty($contentCwh)) {
                    /** if the model is enabled for tags but not for cwh tab name and label are different */
                    $this->items[] = [
                        'label' => \Yii::t('amoscore', 'Tag aree di interesse'),
                        'content' => $content,
                        'options' => ['id' => 'tags'],
                    ];
                } else {
                    $this->items[] = [
                        'label' => \Yii::t('amoscore', 'Destinatari'),
                        'content' => $content,
                        'options' => ['id' => 'destinatari'],
                    ];
                }
            }

            /* if model is UserProfile or extends UserProfile, show a different Widget */
            if (($model instanceof UserProfile) && isset($moduleCwh) && isset($moduleTag)) {
                $this->items[] = [
                    'label' => \Yii::t('amoscore', 'Tag Aree di interesse'),
                    'content' => (new \lispa\amos\cwh\widgets\TagWidgetAreeInteresse([
                    'model' => $model,
                    'attribute' => 'areeDiInteresse',
                    'form' => \yii\base\Widget::$stack[0]
                    ]))->run(),
                ];
            }

            /** @var \lispa\amos\report\AmosReport $moduleReport */
            $moduleReport = \Yii::$app->getModule('report');
            if (isset($moduleReport) && in_array(get_class($model), $moduleReport->modelsEnabled) && !$this->hideReportTab) {
                $this->items[] = [
                    'label' => \Yii::t('amoscore', 'Reports').
                    Html::tag('span', '',
                        ['id' => 'tab-reports-bullet', 'class' => 'badge badge-default badge-panel-heading']),
                    'content' => \lispa\amos\report\widgets\TabReportsWidget::widget([
                        'model' => $model
                    ]),
                    'options' => ['id' => 'tab-reports']
                ];
            }
        }

        foreach ($this->items as $n => $item) {
            if (!ArrayHelper::remove($item, 'visible', true)) {
                continue;
            }
            if (!array_key_exists('label', $item)) {
                throw new InvalidConfigException("The 'label' option is required.");
            }
            $encodeLabel   = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $label         = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $headerOptions = array_merge($this->headerOptions, ArrayHelper::getValue($item, 'headerOptions', []));
            $linkOptions   = array_merge($this->linkOptions, ArrayHelper::getValue($item, 'linkOptions', []));

            if (isset($item['items'])) {
                $label .= ' <b class="caret"></b>';
                Html::addCssClass($headerOptions, 'dropdown');

                if ($this->renderDropdown($n, $item['items'], $panes)) {
                    Html::addCssClass($headerOptions, 'active');
                }

                Html::addCssClass($linkOptions, 'dropdown-toggle');
                $linkOptions['data-toggle'] = 'dropdown';
                $header                     = Html::a($label, "#", $linkOptions)."\n"
                    .Dropdown::widget([
                        'items' => $item['items'],
                        'clientOptions' => false,
                        'view' => $this->getView()
                ]);
            } else {
                $options       = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
                $options['id'] = ArrayHelper::getValue($options, 'id', $this->options['id'].'-tab'.$n);

                Html::addCssClass($options, 'tab-pane');
                if (ArrayHelper::remove($item, 'active')) {
                    Html::addCssClass($options, 'active');
                    Html::addCssClass($headerOptions, 'active');
                }

                if (isset($item['url'])) {
                    $header = Html::a($label, $item['url'], $linkOptions);
                } else {
                    $linkOptions['data-toggle'] = 'tab';
                    $header                     = Html::a($label, '#'.$options['id'], $linkOptions);
                }

                if ($this->renderTabContent) {
                    $panes[] = Html::tag('div', isset($item['content']) ? $item['content'] : '', $options);
                }
            }

            $headers[] = Html::tag('li', $header, $headerOptions);
        }

        return Html::tag('ul', implode("\n", $headers), $this->options)
            .($this->renderTabContent ? "\n".Html::tag('div', implode("\n", $panes),
                ['id' => 'bk-formDefault', 'class' => 'tab-content']) : '');
    }
}