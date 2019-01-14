<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

namespace backend\controllers;


use yii\web\Controller;

class RolesCheckerController extends Controller
{

    public $layout = '@vendor/lispa/amos-core/views/layouts/main';

    public function actionIndex()
    {
        $Roles = \Yii::$app->getAuthManager()->getRoles();

        return $this->render('index', [
            'Roles' => $Roles
        ]);


    }

}