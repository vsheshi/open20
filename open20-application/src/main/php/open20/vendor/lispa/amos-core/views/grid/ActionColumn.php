<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\views\grid
 * @category   CategoryName
 */

namespace lispa\amos\core\views\grid;

use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\views\common\Buttons;
use Yii;
use yii\grid\ActionColumn as YiiActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

//\kartik\grid\ActionColumn

class ActionColumn extends YiiActionColumn
{

    public $buttonClass = 'lispa\amos\core\views\common\Buttons';
    public $viewOptions = [
        'class' => 'btn btn-tools-secondary' //old bk-btnMore
    ];
    public $updateOptions = [
        'class' => 'btn btn-tools-secondary' //old bk-btnEdit
    ];
    public $deleteOptions = [
        'class' => 'btn btn-danger-inverse' //old bk-btnDelete
    ];

    public $_isDropdown = false;

    public $additionalParams = [];
    public $useOnly_additionalParams = false;

    protected function renderDataCellContent($model, $key, $index)
    {
        //if isset the additional params parameter
        if( !empty($this->additionalParams) ){
            //if isset to use only additional params
            if( $this->useOnly_additionalParams ){
                $key = $this->additionalParams;
            }
            else{
                if( is_array($this->additionalParams) ){
                    //usually $key isn't an array: it contains the record id only ad integer value.
                    if( is_array($key) ){
                        $key = array_merge($key,$this->additionalParams );
                    }else{
                        $tmp_array_key = ['id' => $key];
                        $key = array_merge($tmp_array_key,$this->additionalParams );
                    }
                }else{
                    //error: additional params MUST BE array key => value
                }
            }
        }

        $renderDataCellContent = parent:: renderDataCellContent($model, $key, $index);
        return Html::tag('div', $renderDataCellContent, ['class' => 'bk-elementActions container-action']) . Html::tag('div', '', ['class' => 'clearfix']);
    }


    protected function initDefaultButtons()
    {

        $buttonOptions =
            [
                'class'=> $this->buttonClass,
                'template' => $this->template,
                '_isDropdown' => $this->_isDropdown,
                'viewOptions'=> $this->viewOptions,
                'updateOptions'=> $this->updateOptions,
                'deleteOptions'=> $this->deleteOptions,
                'buttons'=>$this->buttons,
            ];

        $Button = Yii::createObject($buttonOptions);
        $Button->initDefaultButtons();
        $this->buttons = $Button->buttons;
    }

}