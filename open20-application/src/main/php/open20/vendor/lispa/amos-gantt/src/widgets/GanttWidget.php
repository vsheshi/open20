<?php

namespace lispa\amos\gantt\widgets;

use lispa\amos\gantt\assets\AmosGanttAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\JsExpression;

/**
 * Class GanttWidget
 *
 * @package lispa\amos\gantt\widgets
 *
 */
class GanttWidget extends \yii\base\Widget
{
    /**
     * @var string the language that the gantt will be displayed.
     *
     * It is recommended that you use [IETF language tags](http://en.wikipedia.org/wiki/IETF_language_tag).
     * For example, `en` stands for English, while `en-US` stands for English (United States).
     *
     * If [[language]] is not set, it will be set as language application
     *
     */
    public $language = null;

    /**
     * @var null
     */
    public $model = null;

    /**
     * @var array
     */
    public $projects = [];
    /**
     * @var array
     */
    public $tasks = [];

    /**
     * @var bool
     */
    public $showToolbar = true;

    /**
     * @var array
     */
    public $defaultClientOptions = [

    ];
    /**
     * @var array
     */
    public $defaultClientEvents = [

    ];
    /**
     * @var array
     */
    public $clientOptions = [

    ];
    /**
     * @var array
     */
    public $clientEvents = [

    ];


    public $drag_links_permissions = null;

    /**
     * @var bool enable fullscreen mode for gantt widget
     *
     */
    public $fullscreen_mode = true;

    /**
     * "minute", "hour", "day", "week", "month", "year"
     *
     * @var string
     */
    public $scale_unit = 'week';

    /**
     * "minute", "hour", "day", "week", "month", "year"
     *
     * @var string
     */
    private $duration_unit = "day";

    private $jsGanttObj = 'gantt';

    /**
     * @return string
     */
    public function getJsGanttObj()
    {
        return $this->jsGanttObj;
    }

    /**
     * @param string $jsGanttObj
     */
    public function setJsGanttObj($jsGanttObj)
    {
        $this->jsGanttObj = $jsGanttObj;
    }

    public function init()
    {
        parent::init();


        $this->defaultClientOptions = [
            'config' => [
                'drag_lightbox' => false,
                'readonly' => !$this->evaluateDragLinksPermission(),
                'drag_links' => $this->evaluateDragLinksPermission(),
                'drag_resize' => false,
                'drag_progress' => false,
                'drag_mode' => new JsExpression("false"),
                'details_on_dblclick' => false,
                'details_on_create' => false,
                'show_quick_info' => false,
                'fit_tasks' => true,
                'preserve_scroll' => true,
                'duration_unit' => $this->duration_unit,
                'scale_unit' => $this->scale_unit,
                'columns' => [
                    [
                        'name' => 'text',
                        'tree' => true,
                        'width' => 250,
                    ],
                    /*
                    [
                        'name' => 'start_date',
                        'align' => 'center',
                        'width' => 150,
                    ],
                    */
                    /*
                    [
                        'name' => 'end_date',
                        'align' => 'center',
                        'width' => 150,
                    ],
                    [
                        'name' => 'duration',
                        'align' => 'center',
                        'width' => 70,
                    ]
                    */
                ]
            ],
            'templates' => [

            ]
        ];

        $this->defaultClientEvents = [
            'onTaskDblClick' => new JsExpression("function (id,event) { 
                var task = gantt.getTask(id);
                //console.log(task.links.edit);                
                location.href = task.links.edit;
            }"),
            'onTemplatesReady' => new JsExpression("function (){
            }"),
            'onBeforeLinkDelete' => new JsExpression("function(id,item){
" . (
                isset($this->model->ajaxCallback['onBeforeLinkDelete']['url'])
                    ?
                    "   var success = true; 
                    
                        jQuery.ajax({
                            async : false,
                            method  : '" . (($this->model->ajaxCallback['onBeforeLinkDelete']['method']) ? $this->model->ajaxCallback['onBeforeLinkDelete']['method'] : 'DELETE') . "',
                            url : '{$this->model->ajaxCallback['onBeforeLinkDelete']['url']}',
                            data : item,
                            success: function (data) {
                                gantt.message({type:'success', text : 'Collegamento cancellato'});
                                success = true;
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                gantt.message({type:'error', text : jqXHR.responseJSON.message});
                                success = false;
                            }
                        });
                        return success; 
"
                    : "
"
                ) .
                "
            }"),
            'onBeforeLinkUpdate' => new JsExpression("function(id,item){
                /*console.log('onAfterLinkUpdate');
                console.log(id);
                console.log(item);
                */
                return false;
            }"),
            'onBeforeLinkAdd' => new JsExpression("function(id,item){
                if({$this->getJsGanttObj()}.getTask(item.target).readonly || {$this->getJsGanttObj()}.getTask(item.source).readonly){
                    gantt.message({type:'error', text:'" . \Yii::t('amosgantt',
                    "Non è possibile collegare attività a task o viceversa") . "'});
                    return false;
                }else{
                    " . (
                isset($this->model->ajaxCallback['onBeforeLinkAdd']['url'])
                    ?
                    "   var success = true; 
                    
                        jQuery.ajax({
                            async : false,
                            method  : '" . (($this->model->ajaxCallback['onBeforeLinkAdd']['method']) ? $this->model->ajaxCallback['onBeforeLinkAdd']['method'] : 'POST') . "',
                            url : '{$this->model->ajaxCallback['onBeforeLinkAdd']['url']}',
                            data : item,
                            success: function (data) {
                                gantt.message({type:'success', text : 'Collegamento creato'});
                                success = true;
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                gantt.message({type:'error', text : jqXHR.responseJSON.message});
                                success = false;
                            }
                        });
                        return success; 
                        
                        
"
                    : "
"
                ) .
                "
                }
                
            }"),
        ];

        $this->clientOptions = ArrayHelper::merge($this->defaultClientOptions, $this->clientOptions);
        $this->clientEvents = ArrayHelper::merge($this->defaultClientEvents, $this->clientEvents);

    }

    public function run()
    {
        $this->registerAssets();

        $this->loadData();

        $this->registerPlugin();


        $html = '';

        if ($this->showToolbar) {
            $html .= $this->render('toolbar/toolbar', [
                'widget' => $this
            ]);
        }
        $html .= \lispa\amos\core\helpers\Html::tag('div', '', [
            'id' => 'gantt_here',
            'style' => [
                'width' => '100%',
                'height' => '100%',
                'min-height' => '600px',
            ],
        ]);

        return $html;

    }

    /**
     * @return AssetBundle the registered asset bundle instance
     */
    public function registerAssets()
    {
        $CurrentView = $this->getView();


        return AmosGanttAsset::register($CurrentView);
    }


    /**
     *
     */
    public function loadData()
    {
        $CurrentView = $this->getView();

        if (isset($this->data)) {
            /**
             * TODO
             * da gestire meglio questo passaggio di dati
             */
            $jsInit = $this->data;
        } elseif ($this->model) {
            $jsInit = $this->model->getGanttValue();
        } else {
            //throw new InvalidConfigException(\Yii::t('amosgantt' , 'No data selected'));
        }
        //$jsInit = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sample' . DIRECTORY_SEPARATOR . 'data.js');

        return $CurrentView->registerJs($jsInit, $CurrentView::POS_READY);
    }


    protected function registerPlugin()
    {
        $CurrentView = $this->getView();

        $jsPreInit = '';
        if (isset($this->clientOptions['config'])) {
            $jsPreInit .= $this->parseOptions($this->clientOptions['config'], "{$this->getJsGanttObj()}.config");
        }

        if (isset($this->clientOptions['templates'])) {
            $jsPreInit .= $this->parseOptions($this->clientOptions['templates'], "{$this->getJsGanttObj()}.templates");
        }

        if (isset($this->clientEvents)) {
            $jsPreInit .= $this->parseEvents($this->clientEvents, "{$this->getJsGanttObj()}");
        }

        $jsInit = <<<JS
\t$jsPreInit
\t{$this->getJsGanttObj()}.init("gantt_here");
\t{$this->getJsGanttObj()}.parse(tasks);
JS;


        return $CurrentView->registerJs($jsInit, $CurrentView::POS_READY);
    }

    /**
     * @param $options
     * @param string $placeholder
     * @return string
     */
    private function parseOptions($options, $placeholder = '')
    {
        $jsParseOptions = '';

        foreach ($options as $k => $v) {
            if (is_bool($v)) {
                $v = ($v ? 'true' : 'false');
            } else {
                if (is_string($v)) {
                    $v = "'{$v}'";
                }
            }
            if (is_array($v)) {
                $v = Json::encode($v);
            }
            $jsParseOptions .= "{$placeholder}.{$k} = {$v};\n\r\t";
        }
        return $jsParseOptions;

    }

    /**
     * @param $events
     * @param string $placeholder
     * @return string
     */
    private function parseEvents($events, $placeholder = '')
    {
        $jsParseEvents = '';

        foreach ($events as $k => $v) {
            $jsParseEvents .= "{$placeholder}.attachEvent('{$k}' , {$v});\n\r\t";
        }
        return $jsParseEvents;

    }


    /**
     * @return bool
     */
    private function evaluateDragLinksPermission(){
        return $this->evaluatePermission($this->drag_links_permissions);
    }

    /**
     * @return bool
     */
    private function evaluatePermission($permis)
    {
        $ret = true;
        if (!is_null($permis)) {
            $user = \Yii::$app->getUser();
            $param = [
                'model' => $this->model
            ];
            $ret = $user->can($permis, $param);
        }
        return $ret;
    }

}