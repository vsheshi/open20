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
use lispa\amos\core\icons\AmosIcons;
use Yii;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

/**
 * Class PublishedContentsWidget
 * @package lispa\amos\core\forms
 */
class PublishedContentsWidget extends Widget
{

    /**
     * @var string classname of the model of the listed objects
     */
    public $modelClass;

    /**
     * @var string label for the model of the listed objects
     */
    public $modelLabel;

    /**
     * @var string $scope Id  of the scope in which it is published the listed objects (eg. a community ID)
     */
    public $scope;

    /**
     * @var string $layout Widget view
     */
    public $layout = "@vendor/lispa/amos-core/forms/views/widgets/published_contents_widget.php";

    /**
     * @var ActiveDataProvider $data Ihe data list to show
     */
    public $data;

    /**
     * @var array $gridViewColumns The column list used in object gridview
     */
    public $gridViewColumns = [];

    /**
     * @var bool|false true if we are in edit mode
     */
    public $isUpdate = false;

    /**
     * @inheritdoc
     */
    public function init()
    {

        $object = Yii::createObject($this->modelClass);

        $this->gridViewColumns = $object->getGridViewColumns();
        $this->modelLabel = $object->getGrammar()->getModelLabel();

        /** @var \lispa\amos\cwh\AmosCwh $moduleCwh */
        $moduleCwh = \Yii::$app->getModule('cwh');

        /** @var \lispa\amos\cwh\query\CwhActiveQuery $cwhActiveQuery */
        $cwhActiveQuery = null;

        if (isset($moduleCwh)) {
            $moduleCwh->setCwhScopeInSession($this->scope);
            $moduleCwh->setCwhScopeFromSession();
            $cwhActiveQuery = new \lispa\amos\cwh\query\CwhActiveQuery(
                $this->modelClass,[
                'queryBase' => $object::find()->distinct()
            ]);
        }

        $this->data = new ActiveDataProvider([
            'query' => $cwhActiveQuery->getQueryCwhAll(),
            'pagination' => ['pageSize' => 5],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC
                ]
            ]
        ]);

        $this->gridViewColumns[] = [
            'class' => 'lispa\amos\core\views\grid\ActionColumn',
            'headerOptions' => [
                'id' => 'favourite'
            ],
            'contentOptions' => [
                'headers' => 'favourite'
            ],
            'template' => '{view}{update}{delete}{favourite}',
            'buttons' => [
                'view' => function ($url, $model) {
                                $createUrlParams = [
                                    $model->getViewUrl(),
                                    'id' => $model['id'],
                                ];
                    $btn = Html::a(
                        AmosIcons::show('file', ['class' => '']),
                        Yii::$app->urlManager->createUrl($createUrlParams),
                        [
                            'title' => Yii::t('amoscore', 'Leggi'),
                            'class' => 'btn btn-tool-secondary',
                            'target' => '_blank'
                        ],
                        true    // Verifica i permessi col nuovo metodo nella classe Html
                    );
                    return $btn;
                },
                'update' => function ($url, $model) {
                    if(!$this->isUpdate){
                        return '';
                    }
                    $createUrlParams = [
                        substr_replace($model->getViewUrl(), '/update',strrpos($model->getViewUrl(), '/')),
                        'id' => $model['id'],
                    ];
                    $btn = Html::a(
                        AmosIcons::show('edit', ['class' => 'btn btn-tool-secondary']),
                        Yii::$app->urlManager->createUrl($createUrlParams),
                        [
                            'title' => Yii::t('amoscore', 'Edit'),
                            'class' => 'bk-btnEdit',
                            'target' => '_blank'
                        ],
                        true    // Verifica i permessi col nuovo metodo nella classe Html
                    );
                    return $btn;
                },
                'delete' => function ($url, $model) {
                    if(!$this->isUpdate){
                        return '';
                    }
                    $createUrlParams = [
                        substr_replace($model->getViewUrl(), '/delete',strrpos($model->getViewUrl(), '/')),
                        'id' => $model['id'],
                    ];
                    $btn = Html::a(
                        AmosIcons::show('delete', ['class' => 'btn btn-tool-secondary']),
                        Yii::$app->urlManager->createUrl($createUrlParams),
                        [
                            'title' => Yii::t('amoscore', 'Delete'),
                            'class' => 'bk-btnDelete',
                            'target' => '_blank'
                        ],
                        true    // Verifica i permessi col nuovo metodo nella classe Html
                    );
                    return $btn;
                },
                'favourite' => function ($url, $model) {
//                    $btn = Html::a(
//                        AmosIcons::show('star', ['class' => 'btn btn-tool-secondary']),
//                                    'javascript:void(0)',
//                                    [
//                                        'title' => Yii::t('amoscore', 'Aggiungi/rimuovi preferito'),
//                                        'class' => 'bk-btnEdit',
//                                    ],
//                        false  //TODO replace with true once url is fine  // Verifica i permessi col nuovo metodo nella classe Html
//                    );
                    return '';
//                    return $btn;
                },
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->renderFile($this->layout, [
            'widget' => $this
        ]);
    }
}