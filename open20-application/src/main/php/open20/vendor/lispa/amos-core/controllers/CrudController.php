<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\controllers
 * @category   CategoryName
 */

namespace lispa\amos\core\controllers;

use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\module\BaseAmosModule;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;


/**
 * Class CrudController
 *
 * @property \lispa\amos\core\record\Record $model
 *
 * @package lispa\amos\core\controllers
 */
abstract class CrudController extends BaseController
{
    const BEFORE_FINDMODEL_EVENT = "beforeFindModel";
    const AFTER_FINDMODEL_EVENT = "afterFindModel";
    
    public $otherViewAvailable = false;
    
    public $dataProvider;
    public $gridViewColumns = null;
    public $modelSearch;
    public $currentView;
    public $availableViews;
    public $url;
    public $parametro;
    
    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        
        if (!isset($this->modelSearch)) {
            throw new InvalidConfigException("{modelSearch} must be set in your init function");
        }
        if (!isset($this->availableViews)) {
            throw new InvalidConfigException("{availableViews}: gridView,listView,mapView,calendarView.. must be set");
        }
        
        $this->initCurrentView();
        
        $this->initAvailableViews();
    }
    
    public function initCurrentView()
    {
        $currentView = $this->getDefaultCurrentView($this->getModelClassName());
        $this->setCurrentView($currentView);
        
        if ($currentViewName = Yii::$app->request->getQueryParam('currentView')) {
            $this->setCurrentView($this->getAvailableView($currentViewName));
        }
    }
    
    protected function getDefaultCurrentView($modelClass)
    {
        $this->initAvailableViews();
        $views = array_keys($this->getAvailableViews());
        $defaultView = (in_array('icon', $views) ? 'icon' : $views[0]);
        return $this->getAvailableView($defaultView);
    }
    
    public function initAvailableViews()
    {
        if (!$this->getAvailableViews()) {
            $this->setAvailableViews([
                'grid' => [
                    'name' => 'grid',
                    'label' => Yii::t('amoscore', '{iconaTabella}' . Html::tag('p', Yii::t('amoscore', 'Table')), [
                        'iconaTabella' => AmosIcons::show('view-list-alt')
                    ]),
                    'url' => '?currentView=grid'
                ],
            ]);
        }
    }
    
    /**
     * @return mixed
     */
    public function getAvailableViews()
    {
        return $this->availableViews;
    }
    
    /**
     * @param mixed $availableViews
     */
    public function setAvailableViews($availableViews)
    {
        $this->availableViews = $availableViews;
    }
    
    public function getAvailableView($name)
    {
        return $this->getAvailableViews()[$name];
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function getParametro()
    {
        return $this->parametro;
    }
    
    public function setParametro($parametro)
    {
        $this->parametro = $parametro;
    }

    public function can($strPermission)
    {
        return (Yii::$app->user->can(strtoupper($this->getModelName() . '_' . $strPermission))
            ||
            Yii::$app->user->can(get_class($this->getModel()) . '_' . strtoupper($strPermission))
        );
    }
    
    /**
     * Finds the ModelClass model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return string the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionIndex($layout = NULL)
    {
        $this->setUpLayout('list');
        
        //se il layout di default non dovesse andar bene si può aggiuntere il layout desiderato
        //in questo modo nel controller "return parent::actionIndex($this->layout);"
        if ($layout) {
            $this->setUpLayout($layout);
        }
        return $this->render('index', [
            'dataProvider' => $this->getDataProvider(),
            'model' => $this->getModelSearch(),
            'currentView' => $this->getCurrentView(),
            'availableViews' => $this->getAvailableViews(),
            'url' => ($this->url) ? $this->url : NULL,
            'parametro' => ($this->parametro) ? $this->parametro : NULL
        ]);
    }
    
    /**
     * @return mixed
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }
    
    /**
     * @param mixed $dataProvider
     */
    public function setDataProvider(ActiveDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }
    
    /**
     * @return mixed
     */
    public function getModelSearch()
    {
        return $this->modelSearch;
    }
    
    /**
     * @param mixed $modelSearch
     */
    public function setModelSearch($modelSearch)
    {
        $this->modelSearch = $modelSearch;
    }
    
    /**
     * @return string
     */
    public function getCurrentView()
    {
        return $this->currentView;
    }
    
    /**
     * @param string $currentView
     */
    public function setCurrentView($currentView)
    {
        $this->currentView = $currentView;
    }
    
    /**
     * Finds the modelObj model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return string the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var \lispa\amos\core\record\Record $model */
        $model = null;
        $modelObj = $this->getModelObj();
        
        $modelObj->id = $id;
        $modelObj->trigger(self::BEFORE_FINDMODEL_EVENT);
        if (($model = $modelObj->findOne($id)) === null) {
            throw new NotFoundHttpException(BaseAmosModule::t('amoscore', 'The requested page does not exist.'));
        }
        $this->setModelObj($model);
        $model->trigger(self::AFTER_FINDMODEL_EVENT);
        return $model;
    }
    
    /**
     * @return array
     */
    public function getGridViewColumns()
    {
        return $this->gridViewColumns;
    }
    
    /**
     * @param array $gridViewColumns
     */
    public function setGridViewColumns($gridViewColumns)
    {
        $this->gridViewColumns = $gridViewColumns;
    }
}
