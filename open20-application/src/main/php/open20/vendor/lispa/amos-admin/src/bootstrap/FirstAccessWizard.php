<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\bootstrap
 * @category   CategoryName
 */

namespace lispa\amos\admin\bootstrap;

use lispa\amos\admin\components\FirstAccessWizardComponent;
use yii\base\BootstrapInterface;
use yii\base\Controller;
use yii\base\Event;
use yii\base\ViewEvent;
use yii\base\View;
use yii\base\WidgetEvent;
use yii\helpers\Url;
use yii\web\User;
use yii\widgets\Breadcrumbs;


class FirstAccessWizard implements BootstrapInterface
{

    /**
     * @param $app
     */
    public function bootstrap($app)
    {
        Event::on(User::className(), User::EVENT_AFTER_LOGIN, [$this, 'startUpWizard']);
    }

    public function startUpWizard($event)
    {
        $adminModule = \Yii::$app->getModule('admin');

        if (!is_null($adminModule)) {
            $userProfileWizard = new FirstAccessWizardComponent();

            $showWizard = $userProfileWizard->showWizard();

            if (!is_null($showWizard)) {
                \Yii::$app->response->send();
            }
        }
    }
}