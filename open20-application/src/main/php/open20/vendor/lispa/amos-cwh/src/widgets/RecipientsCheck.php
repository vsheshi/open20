<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\widgets;


use lispa\amos\core\helpers\Html;
use lispa\amos\cwh\AmosCwh;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\web\View;


/**
 * Class RecipientsCheck
 * @package lispa\amos\cwh\widgets
 */
class RecipientsCheck extends Widget
{
    /**
     * @var \yii\widgets\ActiveForm $form
     */
    protected $form = null;

    /**
     * @var \yii\db\ActiveRecord $model
     */
    protected $model = null;

    /**
     * @var string
     */
    protected $nameField = null;

    public function init()
    {
        parent::init();
        if (!isset($this->nameField)) {
            $refClass = new \ReflectionClass(get_class($this->getModel()));
            $this->setNameField($refClass->getShortName());
        }
    }

    /**
     * @return \yii\db\ActiveRecord
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param \yii\db\ActiveRecord $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    public function run()
    {

        $model = $this->model;
        $refClass = new \ReflectionClass(get_class($model));
        $formPrefix = strtolower($refClass->getShortName());
        $className = addslashes($refClass->name);
        $js = <<<JS
        
        function drawRecipientsPopup() {
            var tags= $('#amos-tag').find('input.hide');
            var tagValues = ''; 
            $.each(tags, function( key, value ) { 
                if(value.value != ''){
                    if(tagValues != ''){
                        tagValues = tagValues + ',';
                    }
                    tagValues = tagValues+ value.value; 
                }
            });
            $.ajax({
                url: '/cwh/cwh-ajax/recipients-check',
                async: true,
                type: 'POST',
                data: {
                    validators:  $("#$formPrefix-validatori").val(),
                    publicationRule: $("#cwh-regola_pubblicazione").val(),
                    scopes: $("#cwh-destinatari").val(),
                    tags: tagValues,
                    className: "$className",
                    searchName: $("#search-recipients").val()
                },
               success: function(response) {
                   if(response) { 
                      $("#recipients-preview").html(response);
                      $("#recipientsPopup").modal('show');  
                   } else{
                       $("#recipientsPopup").modal('show'); 
                   }
               }
            });
        }
        
        $('#recipientsPopup').on("click", "#search-recipients-btn", function(e) {    
            e.preventDefault(); 
            drawRecipientsPopup();
            return false;
        });

         $('#recipientsPopup').on("keypress", "#search-recipients", function(e) {
            if(e.which == 13) {
                e.preventDefault();
                 drawRecipientsPopup();
                return false;
            }
        });

         $('#recipientsPopup').on("click", "#reset-search-recipients-btn", function(e) {    
            e.preventDefault(); 
            $("#search-recipients").val('');
            drawRecipientsPopup();
            return false;
        });
        
        $("#recipients-check").on("click", function(e) {    
            e.preventDefault(); 
            drawRecipientsPopup();
            return false;
        });
        
        $('#recipientsPopup').on("click", ".pagination li a", function(e) {
            e.preventDefault();
            var tags= $('#amos-tag').find('input.hide');
            var tagValues = ''; 
            $.each(tags, function( key, value ) { 
                if(value.value != ''){
                    if(tagValues != ''){
                        tagValues = tagValues + ',';
                    }
                    tagValues = tagValues+ value.value; 
                }
            });
            
            var data = {
                validators:  $("#$formPrefix-validatori").val(),
                publicationRule: $("#cwh-regola_pubblicazione").val(),
                scopes: $("#cwh-destinatari").val(),
                tags: tagValues,
                className: "$className",
                searchName: $("#search-recipients").val()
            };
            
            $.pjax({
                type: 'POST',
                url: $(this).attr('href'),
                container: '#recipients-grid',
                replace: false,
                push: false,
                data: data
            });
            return false;
        });
        
JS;
        $this->getView()->registerJs($js, View::POS_LOAD);

        Modal::begin([
            'id' => 'recipientsPopup',
            'header' => AmosCwh::t('amoscwh', "Recipients check"),
            'size' => Modal::SIZE_LARGE
        ]);
        echo Html::tag('div','', [ 'id' => 'recipients-preview' ]);
        echo Html::tag('div',
            Html::a(AmosCwh::t('amoscwh', 'Close'), null, ['data-dismiss' => 'modal', 'class' => 'btn btn-secondary']),
            ['class' => 'pull-right', 'style' => 'margin: 15px 0']
        );
        Modal::end();
        $btn = Html::a(AmosCwh::t('amoscwh', 'Recipients check'),
            null,
            [
                'class' => 'btn btn-navigation-primary',
                'id' => 'recipients-check'
            ]);
        return $btn;
    }

    /**
     * @return \yii\widgets\ActiveForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param \yii\widgets\ActiveForm $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getNameField()
    {
        return $this->nameField;
    }

    /**
     * @param string $nameField
     */
    public function setNameField($nameField)
    {
        $this->nameField = $nameField;
    }
}