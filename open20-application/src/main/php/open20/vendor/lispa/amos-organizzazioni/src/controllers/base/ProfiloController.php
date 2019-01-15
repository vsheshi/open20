<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\controllers\base;

use lispa\amos\organizzazioni\Module;
use Yii;
use lispa\amos\organizzazioni\models\Profilo;
use lispa\amos\organizzazioni\models\search\ProfiloSearch;
use lispa\amos\core\controllers\CrudController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\helpers\T;
use yii\helpers\Url;

/**
 * ProfiloController implements the CRUD actions for Profilo model.
 */
class ProfiloController extends CrudController
{

    /**
     *
     */
    public function init()
    {
        $model = Module::instance()->createModel('Profilo');

        $this->setModelObj($model);
        $this->setModelSearch(new ProfiloSearch());

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => Yii::t('amoscore', '{iconaTabella}' . Html::tag('p', Yii::t('amoscore', 'Table')), [
                    'iconaTabella' => AmosIcons::show('view-list-alt')
                ]),
                'url' => '?currentView=grid'
            ],
        ]);

        parent::init();
    }


    /**
     * Lists all Profilo models.
     *
     * @param unknown $layout (optional)
     * @return mixed
     */
    public function actionIndex($layout = NULL)
    {
        Url::remember();
        $this->setDataProvider($this->getModelSearch()->search(Yii::$app->request->getQueryParams()));
        return parent::actionIndex();
    }


    /**
     * Displays a single Profilo model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }


    /**
     * Creates a new Profilo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $this->setUpLayout("form");
        $model = Module::instance()->createModel('Profilo');

        //pr(Yii::$app->request->post());die;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item created'));
                return $this->redirect(['index']);
            } else {
                //pr($model->getErrors(),'nosave');die;
                Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not created, check data'));
                return $this->render('create', [
                    'model' => $model,
                    'fid' => NULL,
                    'dataField' => NULL,
                    'dataEntity' => NULL,
                ]);
            }
        } else {
            //pr($model->getErrors(),'novalid');die;
            return $this->render('create', [
                'model' => $model,
                'fid' => NULL,
                'dataField' => NULL,
                'dataEntity' => NULL,
            ]);
        }
    }


    /**
     * Creates a new Profilo model by ajax request.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param unknown $fid
     * @param unknown $dataField
     * @return mixed
     */
    public function actionCreateAjax($fid, $dataField)
    {
        $this->setUpLayout("form");
        $model = Module::instance()->createModel('Profilo');

        if (\Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                //Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item created'));
                return json_encode($model->toArray());
            } else {
                //Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not created, check data'));
                return $this->renderAjax('_formAjax', [
                    'model' => $model,
                    'fid' => $fid,
                    'dataField' => $dataField
                ]);
            }
        } else {
            return $this->renderAjax('_formAjax', [
                'model' => $model,
                'fid' => $fid,
                'dataField' => $dataField
            ]);
        }
    }


    /**
     * Updates an existing Profilo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->setUpLayout("form");
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item updated'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not updated, check data'));
                return $this->render('update', [
                    'model' => $model,
                    'fid' => NULL,
                    'dataField' => NULL,
                    'dataEntity' => NULL,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'fid' => NULL,
                'dataField' => NULL,
                'dataEntity' => NULL,
            ]);
        }
    }


    /**
     * Deletes an existing Profilo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            $model->delete();
            Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item deleted'));
        } else {
            Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not found'));
        }
        return $this->redirect(['index']);
    }


    /**
     * @param null $layout
     * @return bool
     */
    public function setUpLayout($layout = null)
    {
        if ($layout === false) {
            $this->layout = false;
            return true;
        }
        $this->layout = (!empty($layout)) ? $layout : $this->layout;
        $module = \Yii::$app->getModule('layout');
        if (empty($module)) {
            if (strpos($this->layout, '@') === false) {
                $this->layout = '@vendor/lispa/amos-core/views/layouts/'.(!empty($layout) ? $layout : $this->layout);
            }
            return true;
        }
        return true;
    }

}
