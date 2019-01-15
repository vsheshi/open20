<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\giiamos\crud
 * @category   CategoryName
 */

?>
<style>
    [contentEditable=false]:empty:not(:focus):before {
        padding: 10px;
        color: #dddddd;
        font-weight: bold;
        content: attr(data-ph)

    }

    .sortable-area {
        min-height: 20px;
        width: 100%;
        float: left;
        border: 1px dashed #dddddd;
    }

    .on-drag-over {
        border: 2px dashed #4cae4c !important;
    }

    .sortable-area.campi-selezionati li {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }

    .sortable-item {
        list-style-type: none;
        margin: 10px;
        width: auto;
        background: #eee;
        padding: 5px;
        min-height: 10px;
    }

    .fields-area {
        min-height: 20px;
        border: 1px dashed #dddddd;
    }
</style>

<div class="giiamos">

    <?php

    /**
     * @var yii\web\View $this
     * @var yii\widgets\ActiveForm $form
     * @var yii\gii\generators\crud\Generator $generator
     */


    use kartik\widgets\Select2;
    use yii\helpers\Html;
    use yii\helpers\Json;
    use yii\jui\Sortable;
    use yii\web\JsExpression;
    use yii\web\View;    

    
    $this->registerJs('
        var tabsFieldsArray = ' . Json::encode($generator->tabsFieldList) . ';
        var initFromTabsFieldsArray = function (){
            for (key in tabsFieldsArray) {
                if(tabsFieldsArray[key]){
                    var idArea = "giiamos-drag-and-drop-"+key;
                    var elementsToMove = (tabsFieldsArray[key]).split(",");
                    for (column in elementsToMove) {
                        $("#"+idArea).append( $("li[id=\""+elementsToMove[column]+"\"]") );
                    }
                    $("#"+idArea).trigger("sortupdate");
                }
            }
        };
        var inserisciRigaTab = function (tabsText){

            var idTrTab = "giiamos-tr-"+tabsText.replace(/ /g,"");

            if(!$("#"+idTrTab).length){
                var idTdTab = "giiamos-td-tab";
                var idTdFields = "giiamos-td-fields";
                var rowTemplate = "<tr id=\""+ idTrTab+"\"><td>"+ tabsText +"</td><td> " + dragAndDropArea(tabsText) +" </td></tr>";
                $("#giiamos-table").append(rowTemplate);
                (function (){
                    var selectorId = "giiamos-drag-and-drop-"+tabsText.replace(/ /g,"");
                    sortableInit($("#" + selectorId));
                })();
            };
        };

        var rimuoviRigaTab = function (tabsText){
            var idTrTab = "giiamos-tr-"+tabsText.replace(/ /g,"");
            if($("#"+idTrTab).length){
                $("#"+idTrTab).remove();
            };
        };

        var dragAndDropArea = function (tabsText){
            var idTabSanitize = tabsText.replace(/ /g,"");
            var idDragAndDropAreaTab = "giiamos-drag-and-drop-"+idTabSanitize;
            //var idDragAndDropAreaTab = "sortable-area";
            var area = "<div id=\""+idDragAndDropAreaTab+"\" class=\"sortable-area campi-selezionati\" contentEditable=false data-ph=\"Trascina i campi\">";
            area+="</div>";
            area+="<input type=\"hidden\" id= \"input-tag-"+idTabSanitize+"\" name=\"Generator[tabsFieldList]["+idTabSanitize+"]\">";
            return area;
        };

        var sortableInit = function (selector){
                selector.sortable({
                    "connectWith" : ".sortable-area"
                });
                selector.on( "sortover", function( event, ui ) {
                    $(this).addClass("on-drag-over");
                });
                selector.on( "sortout", function( event, ui ) {
                    $(this).removeClass("on-drag-over");
                });
                selector.on("sortupdate", function( event, ui ) {
                        var me = $(this);
                        var inputTabs = me.parent().find("input:first");
                        if(inputTabs.length){
                            var data = $(this).sortable("toArray", {attributes : "id"});
                            $(inputTabs).attr("value",data);
                        }

                });
            };

            $("#elenco-tabs").on("select2-selecting", function(e) {
                inserisciRigaTab(e.val);
            });
            $("#elenco-tabs").on("select2-removed", function(e) {
                rimuoviRigaTab(e.val);
            });

    ', View::POS_END, 'select2-insert-delete-tags');


    echo $form->field($generator, 'modelClass');
    echo $form->field($generator, 'searchModelClass');
    echo $form->field($generator, 'controllerClass');
    echo $form->field($generator, 'baseControllerClass');
    echo $form->field($generator, 'viewPath');
    echo $form->field($generator, 'pathPrefix');
    echo $form->field($generator, 'enableI18N')->checkbox();
    echo $form->field($generator, 'indexWidgetType')->dropDownList(
        [
            'grid' => 'GridView',
            'list' => 'ListView',
        ]
    );
    echo $form->field($generator, 'formLayout')->dropDownList(
        [
            /* Form Types */
            'vertical' => 'vertical',
            'horizontal' => 'horizontal',
            'inline' => 'inline'
        ]
    );
    echo $form->field($generator, 'actionButtonClass')->dropDownList(
        [
            'yii\\grid\\ActionColumn' => 'Default',
            'common\\helpers\\ActionColumn' => 'App Class',
        ]
    );
    echo $form->field($generator, 'providerList')->checkboxList($generator->generateProviderCheckboxListData());
    
    if ($generator->getTableSchema() && $generator->modelClass){ ?>
    <div class="col-lg-12 col-md-12">
        <?= $form->field($generator, 'formTabs')->widget(Select2::classname(), [
            'options' => ['placeholder' => 'Scrivi! ;)', 'id' => 'elenco-tabs'],
            'pluginOptions' => [
                'tags' => [],
                'separator' => "$generator->formTabsSeparator",
                'maximumInputLength' => 20,
                'initSelection' => new JsExpression('function (element, callback) {
                                var data = [];

                                $(element.val().split("' . $generator->formTabsSeparator . '")).each(function () {
                                    data.push({id: this, text: this});
                                    inserisciRigaTab(this);
                                });
                                callback(data);
                                initFromTabsFieldsArray();
                            }'),
            ],
        ]); ?>
    </div>
    <div class="clearfix"></div>

    <div class="col-lg-12 col-md-12">

        <div class="col-lg-6 col-md-6">
            <?php foreach ($generator->getColumnNames() as $field) {
                $itemsField[] = [
                    'content' => $field,
                    'options' => [
                        'id' => $field
                    ],
                ];
            }?>


            <?= Html::tag('label', Yii::t('giiamos', 'Elenco campi')); ?>

            <?= Sortable::widget([
                //'showHandle'=>true,
                'id' => 'tabs-fields',
                'options' => [
                    'tag' => 'div',
                    'class' => 'elenco-campi sortable-area',
                ],
                'clientOptions' => [
                    'cursor' => 'move',
                    'connectWith' => '.sortable-area',
                    'class' => 'sortable-area',
                ],
                'items' => $itemsField,
                'itemOptions' => [
                    'class' => 'sortable-item'
                ]
            ]);

            ?>
        </div>
        <div class="col-lg-6 col-md-6">
            <?php $tableContent = '<tr><th>Tabs</th><th>Campi della form</th></tr>';?>
            <?= Html::tag('table', $tableContent, ['id' => 'giiamos-table', 'class' => 'giiamos-table table table-bordered table-striped']); ?>
        </div>

        <div class="clearfix"></div>



        <?php
        } else { ?>

            <?php \yii\bootstrap\Alert::begin(['options' => [
                'class' => 'alert-info',
            ],]); ?>

            <?= Yii::t('giiamos', '&Egrave; obbligatorio scegliere il model per poter impostare la form'); ?>

            <?php \yii\bootstrap\Alert::end();

        } ?>

    </div>
</div>
<div class="clearfix"></div>