<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comuni
 * @category   CategoryName
 */

namespace lispa\amos\comuni\controllers;

use lispa\amos\comuni\controllers\base\AjaxController;
use yii\web\Controller;


class DefaultController extends AjaxController
{
    public function actionIndex()
    {
        return $this->redirect(['/comuni/dashboard/index']);
    }
}