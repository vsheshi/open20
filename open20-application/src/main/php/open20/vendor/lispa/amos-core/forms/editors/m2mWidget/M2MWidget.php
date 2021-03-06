<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\forms\editors\assets\m2mWidget
 * @category   CategoryName
 */

namespace lispa\amos\core\forms\editors\m2mWidget;

use lispa\amos\core\forms\CreateNewButtonWidget;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\module\BaseAmosModule;
use lispa\amos\core\module\Module;
use lispa\amos\core\record\Record;
use lispa\amos\core\utilities\JsUtility;
use lispa\amos\core\views\AmosGridView;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Class M2MWidget
 * @package lispa\amos\core\forms\editors\m2mWidget
 */
class M2MWidget extends Widget
{
    //model di partenza
    public $model = null;
    //id del model record
    public $modelId = null;
    public $modelData = null;
    public $modelDataArr = null;
    public $permissions = [
        'add' => null
    ];
    /** @var string $targetUrl The url of association page */
    public $targetUrl = null;
    /** @var array $targetUrlParams */
    public $targetUrlParams = null;
    /** @var string $additionalTargetUrl The url of additional association page */
    public $additionalTargetUrl = null;
    //model di scelta associazioni
    public $modelTarget = null;
    //classe di ricerca per il model
    public $modelTargetSearch = [];
    public $modelTargetData = null;
    public $layout = null;
    public $layoutMittente = "{toolbarMittente}\n{itemsMittente}\n{footerMittente}";
    public $layoutTarget = "{toolbarTarget}\n{hiddenInputTarget}\n{itemsTarget}\n{footerTarget}";
    //variabili usate per identificare gli oggetti da salvare nel target
    public $targetColumnsToView = [];
    public $postName = '';
    public $postKey = '';
    public $modelDataArrFromTo = [];

    /**
     * @var Module $moduleClassName
     */
    public $moduleClassName = '';

    public $createNewTargetUrl = '';
    public $targetUrlController = '';
    public $createNewBtnLabel = null;
    public $createNewBtnOtherOptions = null;
    public $itemsMittente = [];
    public $itemsSenderPageSize = 20;
    public $pageParam = 'page';
    public $itemsMittenteActionColumns = [];
    public $mittenteFooter = '';

    /**
     * @var array $relationAttributesArray
     */
    public $relationAttributesArray = [];

    /**
     * @var string $actionColumnsTemplate This is the template used for rendere action buttons. Here is an example:
     * '{relationAttributeManage} {deleteRelation}'. The default is '{deleteRelation}' to view only delete button.
     */
    public $actionColumnsTemplate = '{deleteRelation}';

    /**
     * In case modelData passed is not the relation table target, specify the field of modelData that corresponds to target id
     *
     * @var string $deleteRelationTargetIdField
     */
    public $deleteRelationTargetIdField = null;

    /**
     * @var array $actionColumnsButtons
     */
    public $actionColumnsButtons = [];

    public $createAssociaButtonsEnabled = true;
    public $createAdditionalAssociateButtonsEnabled = false;
    public $disableCreateButton = false;
    public $disableAssociaButton = false;
    public $btnAssociaLabel = '';
    public $btnAssociaClass = 'btn btn-primary';
    public $btnAdditionalAssociateLabel = '';
    public $btnAdditionalAssociateClass = 'btn btn-primary';
    public $forceListRender = false;
    public $overrideModelDataArr = false;
    public $deleteActionName = 'elimina-m2m';
    public $targetFooterButtons = null;
    public $renderTargetCheckbox = true;
    public $targetActionColumnsTemplate = '';
    public $targetActionColumnsButtons = [];

    public $gridId = 'm2m-grid';
    public $firstGridSearch = false;

    /**
     * @var bool $viewSearch
     */
    public $viewSearch = false;

    /**
     * @var bool $singleSelection
     */
    public $multipleSelection = true;

    /**
     * @var bool $withOutModelData
     */
    public $withOutModelData = false;

    /**
     * @var bool $isModal
     */
    public $isModal = false;

    /**
     * @var string $itemsTargetView
     */
    public $itemsTargetView = 'm2mwidget_grid';

    /**
     * @var array|null $listView - option for data provider list view
     */
    public $listView = null;
    
    /**
     * @var array $itemMittenteDefaultOrder
     */
    public $itemMittenteDefaultOrder = null;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        $this->layout = $this->layoutMittente;
        //se sono il widget di destinazione per le scelte delle associazioni
        if (!$this->targetUrl && !$this->forceListRender) {
            $this->layout = $this->layoutTarget;

            if (!isset($this->modelTargetSearch)) {
                throw new InvalidConfigException($this->throwErrorMessage('modelTargetSearch'));
            }
            if (!isset($this->modelTargetSearch['class'])) {
                throw new InvalidConfigException($this->throwErrorMessage('modelTargetSearch[class]'));
            }
            $this->modelTarget = \Yii::createObject($this->modelTargetSearch['class']);
            if (!isset($this->modelTargetSearch['query'])) {
                if (!isset($this->modelTargetSearch['action'])) {
                    throw new InvalidConfigException($this->throwErrorMessage('modelTargetSearch[action]'));
                }
                $this->modelTargetData = $this->modelTarget->{$this->modelTargetSearch['action']}(\Yii::$app->request->getQueryParams());
            } else {
                $this->modelTargetData = new ActiveDataProvider([
                    'query' => $this->modelTargetSearch['query'],
                    'pagination' => [
                        'pageSize' => $this->isModal ? 10 : 20
                    ]
                ]);
            }
        }

        if (!isset($this->modelData) && !$this->withOutModelData) {
            throw new InvalidConfigException($this->throwErrorMessage('modelData'));
        }

        if (!isset($this->modelId)) {
            throw new InvalidConfigException($this->throwErrorMessage('modelId'));
        }

        if (!isset($this->model)) {
            throw new InvalidConfigException($this->throwErrorMessage('model'));
        }

        if (!$this->overrideModelDataArr) {
            $this->modelDataArr = ArrayHelper::map($this->modelData->all(), $this->modelDataArrFromTo['from'], $this->modelDataArrFromTo['to']);
        }
    }

    protected function throwErrorMessage($field)
    {
        return \Yii::t('amoscore', 'Configurazione widget non corretta, {campo} mancante', [
            'campo' => $field
        ]);
    }

    public function run()
    {
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
    public function renderSection($name)
    {
        switch ($name) {
            case '{toolbarMittente}':
                return $this->renderToolbarMittente();
            case '{itemsMittente}':
                return $this->renderItemsMittente();
            case '{toolbarTarget}':
                return $this->renderToolbarTarget();
            case '{hiddenInputTarget}':
                return $this->renderHiddenInputTarget();
            case '{itemsTarget}':
                return $this->renderItemsTarget();
            case '{footerTarget}':
                return $this->renderFooterTarget();
            case '{footerMittente}':
                return $this->renderFooterMittente();
            default:
                return false;
        }
    }

    /**
     * Renders the toolbar
     */
    public function renderToolbarMittente()
    {
        $retVal = '';
        $buttons = '';
        $btnAssociaLabel = ($this->btnAssociaLabel == '') ? Yii::t('amoscore', 'Associa') : $this->btnAssociaLabel;

        if ($this->createAssociaButtonsEnabled) {
            if (!$this->disableCreateButton) {
                $createOptions = [
                    'urlCreateNew' => $this->createNewTargetUrl,
                    'checkPermWithNewMethod' => true,
                    'createButtonId' => self::creaButtonId(),
                    'createNewBtnLabel' => $this->createNewBtnLabel,
                    'otherOptions' => $this->createNewBtnOtherOptions,
                ];
                if (isset($this->model)) {
                    $createOptions['model'] = $this->model;
                }
                // Render create new button only if user has the correct permission. The correct permission check is done by widget.
                $buttons = CreateNewButtonWidget::widget($createOptions);
            }

            // Render "associa" button if the user has the correct permission set in this widget config array.
            if (\Yii::$app->getUser()->can($this->permissions['add'], ['model' => $this->model]) && !$this->disableAssociaButton) {
                $basicUrlParams = [$this->targetUrl, 'id' => $this->modelId];
                $url = $basicUrlParams;
                if (isset($this->targetUrlParams)) {
                    $url = ArrayHelper::merge($basicUrlParams, $this->targetUrlParams);
                }
                $associateBtnId = $this->isModal ? $this->gridId . '-btn-associate' : self::associaButtonId();
                $buttons .= Html::a($btnAssociaLabel, $url, [
                    'class' => $this->btnAssociaClass,
                    'title' => $btnAssociaLabel,
                    'id' => $associateBtnId
                ]);

                if ($this->isModal) {
                    $buttons .= Html::tag('div', '', ['id' => $this->gridId . '-modal-container']);
                    $urlTo = Yii::$app->urlManager->createUrl($url);
                    $js = JsUtility::getM2mAssociateBtnModal($this->gridId, $urlTo);
                    $this->getView()->registerJs($js);
                }

            }

            if ($this->createAdditionalAssociateButtonsEnabled) {
                // Render "associa" button if the user has the correct permission set in this widget config array.
                if (\Yii::$app->getUser()->can($this->permissions['add'], ['model' => $this->model]) && !$this->disableAssociaButton) {
                    $buttons .= Html::a($this->btnAdditionalAssociateLabel, [$this->additionalTargetUrl, 'id' => $this->modelId], [
                        'class' => $this->btnAdditionalAssociateClass,
                        'title' => $this->btnAdditionalAssociateLabel,
                        'id' => self::additionalAssociateButtonId()
                    ]);
                }
            }
        }

        if ($this->firstGridSearch) {
            $gridId = $this->gridId;
            $searchFieldValue = '';
            if (isset($_POST['searchName'])) {
                $searchName = $_POST['searchName'];
                if (isset($_POST[$searchName])) {
                    $searchFieldValue = $_POST[$searchName];
                }
            }


            $buttons = '<div class="col-xs-12 nop"><div class="col-sm-6 btn-add-admin">' . $buttons . '</div><div class="col-sm-6 btn-search-admin">' .
                Html::input('text', null, $searchFieldValue, [
                    'id' => 'search-' . $gridId,
                    'class' => 'form-control pull-left',
                    //'style' => 'width: auto',
                    'placeholder' => BaseAmosModule::t('amoscore', 'Search') . '...',
                ]) .
                Html::a(AmosIcons::show('search', ['class' => 'font13']),
                    null,
                    [
                        'id' => 'search-btn-' . $gridId,
                        'class' => 'btn btn-navigation-primary m-l-5',
                    ]) .
                Html::a(AmosIcons::show('close', ['class' => 'btn-cancel-search']),
                    null,
                    [
                        'id' => 'reset-search-btn-' . $gridId,
                        'class' => 'btn btn-secondary',
                        'alt' => BaseAmosModule::t('amoscore', 'Cancel search')
                    ]) . '</div></div>';
        }

        if (strlen($buttons)) {
            $retVal = Html::tag('div', $buttons, ['class' => 'container-tools']);
        }

        return $retVal;
    }

    public static function creaButtonId()
    {
        return 'm2m-widget-btn-crea';
    }

    public static function associaButtonId()
    {
        return 'm2m-widget-btn-associa';
    }

    public static function gestisciAttributiButtonId()
    {
        return 'm2m-widget-btn-gestisci-attributi';
    }

    public static function additionalAssociateButtonId()
    {
        return 'm2m-widget-btn-additional-associate';
    }

    /**
     * Renders the data models for the grid view.
     */
    public function renderItemsMittente()
    {
        $columns = ArrayHelper::merge(
            $this->itemsMittente,
            $this->getRelationAttributesArray(),
            $this->createActionColumnButtons()
        );

        return AmosGridView::widget([
            'id' => $this->gridId . '-first',
            'dataProvider' => $this->getItemsMittenteDataProvider(),
            'columns' => $columns
        ]);
    }

    private function getItemsMittenteDataProvider()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->modelData,
            'pagination' => [
                'pageSize' => $this->itemsSenderPageSize,
                'pageParam' => $this->pageParam
            ]
        ]);
        if (isset($this->itemMittenteDefaultOrder)) {
            $dataProvider->setSort([
                'defaultOrder' => $this->itemMittenteDefaultOrder
            ]);
        }
        return $dataProvider;
    }

    /**
     * @return array
     */
    private function getRelationAttributesArray()
    {
        return $this->relationAttributesArray;
    }

    /**
     * @return array
     */
    private function createActionColumnButtons()
    {
        $actionButtons = [];

        if (strpos($this->actionColumnsTemplate, 'relationAttributeManage') !== false) {
            $actionButtons = ArrayHelper::merge($actionButtons, $this->m2mAttributesManageActionButton());
        }
        if (strpos($this->actionColumnsTemplate, 'deleteRelation') !== false) {
            $actionButtons = ArrayHelper::merge($actionButtons, $this->deleteActionButton());
        }

        // This is other action columns created by developer for other needs.
        if (!empty($this->actionColumnsButtons)) {
            $actionButtons = ArrayHelper::merge($actionButtons, $this->actionColumnsButtons);
        }

        if (!empty($actionButtons)) {
            $completeActionButtonsArray = [
                'class' => 'lispa\amos\core\views\grid\ActionColumn',
                'template' => $this->actionColumnsTemplate,
                'buttons' => $actionButtons
            ];
            return [$completeActionButtonsArray];
        } else {
            return [];
        }
    }

    /**
     * This method create the default delete action button. It checks if user has the correct rights to view this buttons.
     * This buttons can be viewed by {deleteRelation} key in the actionColumnsTemplate widget config key.
     *
     * @return array
     */
    private function m2mAttributesManageActionButton()
    {
        $actionButtons = [
            'relationAttributeManage' => function ($url, $functionModel) {
                $moduleClassName = $this->moduleClassName;
                $url = Url::current();
                if (Yii::$app->getUser()->can($this->permissions['manageAttributes'], ['model' => $this->model])) {
                    return Html::a('<p class="btn btn-tool-secondary">' . AmosIcons::show('edit') . '</p>', Yii::$app->urlManager->createUrl(['/' . $moduleClassName::getModuleName() . '/' . $this->targetUrlController . '/manage-m2m-attributes', 'id' => $this->model['id'], 'targetId' => $functionModel->id]), [
                        'title' => Yii::t('amoscore', 'Gestisci attributi'),
                        'id' => self::gestisciAttributiButtonId()
                    ]);
                } else {
                    return '';
                }
            }
        ];
        return $actionButtons;
    }

    /**
     * This method create the default delete action button. It checks if user has the correct rights to view this buttons.
     * This buttons can be viewed by {deleteRelation} key in the actionColumnsTemplate widget config key.
     *
     * @return array
     */
    private function deleteActionButton()
    {
        $actionButtons = [
            'deleteRelation' => function ($url, $functionModel) {
                $moduleClassName = $this->moduleClassName;
                $url = '/' . $moduleClassName::getModuleName() . '/' . $this->targetUrlController . '/' . $this->deleteActionName;
                $targetId = $functionModel->id;
                if (isset($this->deleteRelationTargetIdField)) {
                    $targetIdField = $this->deleteRelationTargetIdField;
                    $targetId = $functionModel->$targetIdField;
                }
                $urlDelete = Yii::$app->urlManager->createUrl([
                    $url,
                    'id' => $this->model['id'],
                    'targetId' => $targetId
                ]);
                if (Yii::$app->getUser()->can($this->permissions['add'], ['model' => $this->model])) {
                    return Html::a(
                        '<p class="btn btn-tool-secondary">' . AmosIcons::show('delete') . '</p>',
                        $urlDelete,
                        [
                            'title' => Yii::t('amoscore', 'Elimina associazione'),
                            'data-confirm' => Yii::t('amoscore', 'Sei sicuro di voler cancellare questo elemento?'),
                        ]
                    );
                } else {
                    return '';
                }
            }
        ];
        return $actionButtons;
    }

    /**
     * Renders the toolbar
     */
    public function renderToolbarTarget()
    {
        if (!$this->isModal) {
            return '<div class="form-container">' . Html::beginForm('', 'post', ['class' => 'bk-formDefault tab-content']);
        }
        return '';
    }

    /**
     * Renders the input hidden section
     */
    public function renderHiddenInputTarget()
    {

        if ($this->renderTargetCheckbox) {
            $hiddenInputSection = "";
            foreach ($this->modelDataArr as $id => $label) {
                $hiddenInputSection .= Html::tag("input", null,
                    ['value' => $id, 'type' => 'hidden', 'name' => $this->postName . '[' . $this->postKey . '][]']);
            }

            return Html::tag('div', $hiddenInputSection, ['class' => 'hiddenInputContainer']);
        } else {
            return '';
        }
    }

    /**
     * Renders the data models for the grid view.
     */
    public function renderItemsTarget()
    {
        $this->getView()->params['modelDataArr'] = $this->modelDataArr;
        $this->getView()->params['modelTargetData'] = $this->modelTargetData;
        $this->getView()->params['postKey'] = $this->postKey;
        $this->getView()->params['postName'] = $this->postName;
        $this->getView()->params['columnsArray'] = $this->createTargetColumnsArray();

        $Grid = $this->render($this->itemsTargetView, [
            'searchModel' => $this->modelTarget,
            'viewSearch' => $this->viewSearch,
            'isModal' => $this->isModal,
            'firstGridId' => $this->gridId,
            'useCheckbox' => $this->renderTargetCheckbox,
            'listView' => $this->listView
        ]);
        return $Grid;
    }

    /**
     * Create the complete columns array. It merges a standard checkboxes column with other column arrived by params.
     *
     * @return array
     */
    private function createTargetColumnsArray()
    {
        if ($this->renderTargetCheckbox) {
            $columns = [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model, $key, $index, $column) {
                        $checkboxOptions = [
                            'value' => $model['id'],
                            'checked' => array_key_exists($model['id'], $this->modelDataArr) || (isset($_POST['selection']) && in_array($model['id'], $_POST['selection'])) ? 'checked' : '',
                            'onchange' => new JsExpression('Correlazioni.gestisciSelezione(this, "' . $this->postName . '", "' . $this->postKey . '", ' . ($this->multipleSelection ? 'false' : 'true') . ');'),
                            'class' => 'm2m-target-checkbox'
                        ];
                        return $checkboxOptions;
                    },
                    'multiple' => $this->multipleSelection
                ]
            ];
            $columns = ArrayHelper::merge($columns, $this->targetColumnsToView);
        } else {
            $columns = $this->targetColumnsToView;
        }
        if (!empty($this->actionColumnsTemplate) && !empty($this->actionColumnsButtons)) {
            $actionColumns = [
                'class' => 'lispa\amos\core\views\grid\ActionColumn',
                'template' => $this->actionColumnsTemplate,
                'buttons' => $this->actionColumnsButtons
            ];
            $columns = ArrayHelper::merge($columns, $actionColumns);
        }

        return $columns;
    }

    /**
     * Renders the toolbar
     */
    public function renderFooterTarget()
    {
        if (!empty($this->targetFooterButtons)) {
            $buttons = $this->targetFooterButtons;
        } else {
            $moduleClassName = $this->moduleClassName;
            $cancelButton = self::makeCancelButton($moduleClassName, $this->targetUrlController, $this->model, $this->isModal);

            $url = Yii::$app->urlManager->createUrl([
                '/' . $moduleClassName::getModuleName() . '/' . $this->targetUrlController . '/associa-m2m',
                'id' => $this->model['id'],
            ]);
            $buttons = self::makeSaveButton($this->isModal, $url) . $cancelButton;
        }
        if ($this->isModal) {
            Modal::end();
            return Html::tag('div', $buttons, ['class' => 'bk-btnFormContainer']);
        }
        return Html::tag('div', $buttons, ['class' => 'bk-btnFormContainer']) . Html::endForm() . '</div>';
    }

    /**
     * Renders the toolbar
     */
    public function renderFooterMittente()
    {
        return $this->mittenteFooter;
    }

    /**
     * This method return the default cancel button
     * @param string $moduleClassName
     * @param string $targetUrlController
     * @param Record $model
     * @param bool|false $isModal
     * @return string
     */
    public static function makeCancelButton($moduleClassName, $targetUrlController, $model, $isModal = false)
    {
        /** @var Module $moduleClassName */
        if ($isModal) {
            $button = Html::a(BaseAmosModule::tHtml('amoscore', 'Annulla'), null,
                ['class' => 'btn btn-secondary', 'title' => BaseAmosModule::t('amoscore', 'Annulla'), 'data-dismiss' => 'modal']);
        } else {
            $button = Html::a(BaseAmosModule::tHtml('amoscore', 'Annulla'), Yii::$app->urlManager->createUrl([
                '/' . $moduleClassName::getModuleName() . '/' . $targetUrlController . '/annulla-m2m',
                'id' => $model['id'],
                'action' => Yii::$app->controller->action->id
            ]), ['class' => 'btn btn-secondary', 'title' => Yii::t('amoscore', 'Annulla')]);
        }
        return $button;
    }

    /**
     * This method return the default save button
     * @param bool|false $isModal
     * @return string
     */
    public static function makeSaveButton($isModal = false, $url = null)
    {
        if ($isModal) {
            return Html::a(BaseAmosModule::tHtml('amoscore', 'Salva'), $url,
                ['class' => 'btn btn-navigation-primary save-modal m-l-5']);
        } else {
            return Html::submitButton(BaseAmosModule::tHtml('amoscore', 'Salva'),
                ['class' => 'btn btn-navigation-primary save-association']);
        }
    }
}
