<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\views\common
 * @category   CategoryName
 */

namespace lispa\amos\core\views\common;

use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\utilities\CurrentUser;
use Yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class Buttons
 * @package lispa\amos\core\views\common
 */
class Buttons extends Object
{
    public $urlCreator;
    public $controller;

    public $buttons;
    public $template;

    public $_isDropdown;

    public $viewOptions;
    public $updateOptions;
    public $deleteOptions;

    public function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model) {
                if (!$this->can($model, 'read')) {
                    return '';
                }
                $options = $this->viewOptions;
                if (isset($options['hide']) && $options['hide']) {
                    return '';
                }
                $title = Yii::t('amoscore', 'Leggi');
                $icon = AmosIcons::show('file') . '<span class="sr-only">' . Yii::t('amoscore', 'Leggi') . '</span>';
                $label = ArrayHelper::remove($options, 'label', ($this->_isDropdown ? $icon . ' ' . $title : $icon));
                $viewUrl = $url;
                if (isset($options['url']) && is_array($options['url'])) {
                    $customUrl = ArrayHelper::remove($options, 'url', $url);
                    if (isset($options['defaultUrlIdParam']) && $options['defaultUrlIdParam']) {
                        $defaultUrlIdParam = ArrayHelper::remove($options, 'defaultUrlIdParam', false);
                        if ($defaultUrlIdParam) {
                            $customUrl['id'] = $model->id;
                        }
                    }
                    $viewUrl = $customUrl;
                }
                $options = ArrayHelper::merge(['title' => $title, 'data-pjax' => '0'], $options);
                if ($this->_isDropdown) {
                    $options['tabindex'] = '-1';
                    return '<li>' . Html::a($label, $viewUrl, $options) . '</li>' . PHP_EOL;
                } else {
                    return Html::a($label, $viewUrl, $options);
                }
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model) {
                if (!$this->can($model, 'update')) {
                    return '';
                }
                $options = $this->updateOptions;
                if (isset($options['hide']) && $options['hide']) {
                    return '';
                }
                $title = Yii::t('amoscore', 'Modifica');
                $icon = AmosIcons::show('edit') . '<span class="sr-only">' . Yii::t('amoscore', 'Modifica') . '</span>';
                $label = ArrayHelper::remove($options, 'label', ($this->_isDropdown ? $icon . ' ' . $title : $icon));
                $updateUrl = $url;
                if (isset($options['url']) && is_array($options['url'])) {
                    $customUrl = ArrayHelper::remove($options, 'url', $url);
                    if (isset($options['defaultUrlIdParam']) && $options['defaultUrlIdParam']) {
                        $defaultUrlIdParam = ArrayHelper::remove($options, 'defaultUrlIdParam', false);
                        if ($defaultUrlIdParam) {
                            $customUrl['id'] = $model->id;
                        }
                    }
                    $updateUrl = $customUrl;
                }
                $options = ArrayHelper::merge(['title' => $title, 'data-pjax' => '0'], $options);
                if ($this->_isDropdown) {
                    $options['tabindex'] = '-1';
                    return '<li>' . Html::a($label, $updateUrl, $options) . '</li>' . PHP_EOL;
                } else {
                    return Html::a($label, $updateUrl, $options);
                }
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model) {
                if (!$this->can($model, 'delete')) {
                    return '';
                }
                $options = $this->deleteOptions;
                if (isset($options['hide']) && $options['hide']) {
                    return '';
                }
                $title = Yii::t('amoscore', 'Cancella');
                $icon = AmosIcons::show('delete') . '<span class="sr-only">' . Yii::t('amoscore', 'Cancella') . '</span>';
                $label = ArrayHelper::remove($options, 'label', ($this->_isDropdown ? $icon . ' ' . $title : $icon));
                $deleteUrl = $url;
                if (isset($options['url']) && is_array($options['url'])) {
                    $customUrl = ArrayHelper::remove($options, 'url', $url);
                    if (isset($options['defaultUrlIdParam']) && $options['defaultUrlIdParam']) {
                        $defaultUrlIdParam = ArrayHelper::remove($options, 'defaultUrlIdParam', false);
                        if ($defaultUrlIdParam) {
                            $customUrl['id'] = $model->id;
                        }
                    }
                    $deleteUrl = $customUrl;
                }
                $options = ArrayHelper::merge(
                    [
                        'title' => $title,
                        'data-confirm' => Yii::t('amoscore', 'Sei sicuro di voler cancellare questo elemento?'),
                        'data-method' => 'post',
                        'data-pjax' => '0'
                    ],
                    $options
                );
                if ($this->_isDropdown) {
                    $options['tabindex'] = '-1';
                    return '<li>' . Html::a($label, $deleteUrl, $options) . '</li>' . PHP_EOL;
                } else {
                    return Html::a($label, $deleteUrl, $options);
                }
            };
        }
    }

    /**
     * @param $obj
     * @return mixed|string
     */
    public function get_real_class($obj)
    {
        if (is_array($obj)) {
            $classname = isset($obj['classname']) ? $obj['classname'] : '';
        } elseif (is_object($obj)) {
            $classname = get_class($obj);
        }
        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $classname = $matches[1];
        }
        return $classname;
    }

    /**
     * @param $model
     * @param $action
     * @return bool
     */
    protected function can($model, $action)
    {
        $modelClassName = $this->get_real_class($model);
        $permissionName = strtoupper($modelClassName . '_' . $action);
        return (
            CurrentUser::getUser()->can($permissionName, ['model' => $model])
            ||
            CurrentUser::getUser()->can($modelClassName . '_' . strtoupper($action))
        );
    }

    /**
     * @param $model
     * @param $key
     * @param $index
     * @return string
     */
    public function renderButtonsContent($model, $key, $index)
    {
        $content = preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];
            if (isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);

                return call_user_func($this->buttons[$name], $url, $model, $key);
            } else {
                return '';
            }
        }, $this->template);
        return Html::tag('div', $content, ['class' => 'buttons-container']);

    }

    /**
     * @param $action
     * @param $model
     * @param $key
     * @param $index
     * @return mixed|string
     */
    public function createUrl($action, $model, $key, $index)
    {
        if ($this->urlCreator instanceof \Closure) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string)$key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            return Url::toRoute($params);
        }
    }
}
