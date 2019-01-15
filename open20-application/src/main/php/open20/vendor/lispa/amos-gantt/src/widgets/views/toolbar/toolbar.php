<?php
/**
 *
 * @var $this \yii\web\View
 * @var $widget \lispa\amos\gantt\widgets\GanttWidget
 *
 */

$toolbarScript = <<<JS
    function zoom_tasks(node) {
        var ganttObj = {$widget->getJsGanttObj()};

        switch (node.value) {
            case "hour":
                ganttObj.config.scale_unit = "month";
                ganttObj.config.date_scale = "%F %Y";
                ganttObj.config.step = 1;

                ganttObj.config.scale_height = 60;
                ganttObj.config.min_column_width = 30;
                ganttObj.config.subscales = [
                    {
                        unit: "day",
                        step: 1,
                        date: "%d %F"
                    },
                    {
                        unit: "hour",
                        step: 1,
                        date: "%H"
                    }
                ];
                highlightSelectedScale('#hours-btn');
                break;
            case "day":
                ganttObj.config.scale_unit = "month";
                ganttObj.config.date_scale = "%F %Y";
                ganttObj.config.step = 1;

                ganttObj.config.scale_height = 60;
                ganttObj.config.min_column_width = 30;
                ganttObj.config.subscales = [
                    {
                        unit: "day",
                        step: 1,
                        date: "%d"
                    }
                ];
                highlightSelectedScale('#days-btn');
                break;
                
            case "week":
                ganttObj.config.scale_unit = "month";
                ganttObj.config.date_scale = "%F %Y";
                ganttObj.config.step = 1;

                ganttObj.config.scale_height = 60;
                ganttObj.config.min_column_width = 30;

                var weekScaleTemplate = function(date){
                    var dateToStr = ganttObj.date.date_to_str("%d %M");
                    var endDate = ganttObj.date.add(ganttObj.date.add(date, 1, "week"), -1, "day");
                    return dateToStr(date) + " - " + dateToStr(endDate);
                };
                
                ganttObj.config.subscales = [
                    {
                        unit: "week",
                        step: 1,
                        template:weekScaleTemplate
                    }
                ];
                 highlightSelectedScale('#weeks-btn');
                break;
                
            case "month":
                ganttObj.config.scale_unit = "year";
                ganttObj.config.date_scale = "%Y";
                ganttObj.config.step = 1;

                ganttObj.config.scale_height = 60;
                ganttObj.config.min_column_width = 30;
                ganttObj.config.subscales = [
                    {
                        unit: "month",
                        step: 1,
                        date: "%F"
                    }
                ];
                 highlightSelectedScale('#months-btn');
                break;
            
            case "year":
                console.log();
                ganttObj.config.scale_unit = "year";
                ganttObj.config.date_scale = "%Y";
                ganttObj.config.step = 1;

                ganttObj.config.scale_height = 60;
                ganttObj.config.min_column_width = 30;
                ganttObj.config.subscales = [];
                highlightSelectedScale('#years-btn');
                break;
        }
        ganttObj.render();
    }
    
    function highlightSelectedScale(btnId){
        $('.btn.btn-secondary').removeClass('selected');
        $(btnId).addClass('selected');
    }
JS;


$this->registerJs($toolbarScript, $this::POS_HEAD);
$this->registerJs
(new \yii\web\JsExpression('

    var button = jQuery.find("button[value=\'' . $widget->scale_unit . '\']");
    if(button.length){
        jQuery(button).trigger("click");
    }

'),
    $this::POS_READY);

?>


<script type="text/javascript">


</script>

<div class='controls_bar pull-right'>
    <?php if ($widget->fullscreen_mode) : ?>
        <?= \yii\bootstrap\ButtonGroup::widget([
            'options' => [
                'class' => 'btn-change-scale-group btn-group'
            ],
            'encodeLabels' => false,
            'buttons' => [
                [
                    'label' => \lispa\amos\core\icons\AmosIcons::show('zoom-in'),
                    'options' => [
                        'onclick' => new \yii\web\JsExpression($widget->getJsGanttObj() . '.expand(); this.blur()'),
                        'class' => 'btn btn-secondary',
                        'data' => [
                            'toggle' => 'tooltip',
                            'placement' => 'top',
                            'original-title' => Yii::t('amosgantt', 'Fullscreen mode')
                        ]
                    ]
                ]
            ]
        ]); ?>

    <?php endif; ?>
</div>


<div class='controls-bar-separator pull-right'>&nbsp;</div>


<div class='controls_bar pull-right'>
    <?= \yii\bootstrap\ButtonGroup::widget([
        'options' => [
            'class' => 'btn-change-scale-group btn-group'
        ],
        'encodeLabels' => false,
        'buttons' => [
            /*
            [
                'label' => Yii::t('amosgantt', '<small>Hours</small>'),
                'options' => [
                    'onclick' => new \yii\web\JsExpression('zoom_tasks(this)'),
                    'value' => 'hour',
                    'class' => 'btn btn-secondary',
                    'id' => 'hours-btn'
                ]
            ],
            */
            [
                'label' => Yii::t('amosgantt', '<small>Days</small>'),
                'options' => [
                    'onclick' => new \yii\web\JsExpression('zoom_tasks(this)'),
                    'value' => 'day',
                    'class' => 'btn btn-secondary',
                    'id' => 'days-btn'
                ]
            ],
            [
                'label' => Yii::t('amosgantt',
                    '<small>Week</small>'),
                'options' => [
                    'onclick' => new \yii\web\JsExpression('zoom_tasks(this)'),
                    'value' => 'week',
                    'class' => 'btn btn-secondary',
                    'id' => 'weeks-btn'
                ]
            ],
            [
                'label' => Yii::t('amosgantt', '<small>Months</small>'),
                'options' => [
                    'onclick' => new \yii\web\JsExpression('zoom_tasks(this)'),
                    'value' => 'month',
                    'class' => 'btn btn-secondary',
                    'id' => 'months-btn'
                ]
            ],
            [
                'label' => Yii::t('amosgantt', '<small>Years</small>'),
                'options' => [
                    'onclick' => new \yii\web\JsExpression('zoom_tasks(this)'),
                    'value' => 'year',
                    'class' => 'btn btn-secondary',
                    'id' => 'years-btn'
                ]
            ],
        ]
    ]); ?>


</div>

<div class="clearfix"></div>

<hr/>

<div class="clearfix"></div>

