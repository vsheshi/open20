<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\views\toolbars
 * @category   CategoryName
 */

namespace lispa\amos\core\views\toolbars;


use yii\base\Object;
use yii\helpers\ArrayHelper;

class StatsPanel extends Object implements IStatsPanel
{

    private $icon;
    private $label;
    private $description;
    private $count;
    private $url;

    public function getIcon()
    {
       return $this->icon;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }


    /**
     * @param $type
     * @return string
     */
    public function render($type)
    {
        $html = '';

        if($type){
            $html = $this->renderJavascript();
        }else{
            $html = $this->renderHtml();
        }
        return $html;
    }

    /**
     * @return string
     */
    protected function renderHtml(){
        $url = $this->url;
        $options = [
            'title' => $this->description
        ];
        return \lispa\amos\core\helpers\Html::a(
            "{$this->icon} {$this->count} {$this->label}",
            $url,$options);
    }

    /**
     * @return string
     */
    protected function renderJavascript(){

        $url = null;
        $options = [
            'title' => $this->description,
            'href' => "javascript:$('[data-toggle=\"tab\"], [data-toggle=\"pill\"]').filter('[href=\"#' + '" . $this->url . "'.match(/#(.*)/)[1] + '\"]').tab('show');"
        ];
        return \lispa\amos\core\helpers\Html::a(
            "{$this->icon} {$this->count} {$this->label}",
            $url,$options);
    }
}