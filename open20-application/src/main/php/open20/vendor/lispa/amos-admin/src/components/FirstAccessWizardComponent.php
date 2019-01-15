<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\components
 * @category   CategoryName
 */

namespace lispa\amos\admin\components;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
//use yii\base\BootstrapInterface;
use yii\base\Component;
//use yii\base\Event;

/**
 * Class FirstAccessWizardComponent
 * @package lispa\amos\admin\components
 */
class FirstAccessWizardComponent extends Component /*implements BootstrapInterface*/
{
    /**
     * @param string $moduleClassName
     */
    public function showWizard($moduleClassName = null)
    {
        /** @var \lispa\amos\core\user\User $loggedUser */
        $loggedUser = \Yii::$app->getUser()->identity;
        /** @var \lispa\amos\admin\models\UserProfile $loggedUserProfile */
        $loggedUserProfile = $loggedUser->getProfile();
        if (!$loggedUserProfile->validato_almeno_una_volta && ($loggedUserProfile->status == UserProfile::USERPROFILE_WORKFLOW_STATUS_DRAFT)) {
            if (is_null($moduleClassName)) {
                $moduleClassName = AmosAdmin::className();
            }
            /** @var \lispa\amos\core\module\AmosModule $moduleClassName */
            return \Yii::$app->controller->redirect(['/' . $moduleClassName::getModuleName() . '/first-access-wizard/introduction', 'id' => $loggedUser->profile->id]);
        }
        return null;
    }
    
//    /**
//     * @param \yii\web\Application $app
//     */
//    public function bootstrap($app)
//    {
//        Event::on(\yii\web\User::className(), \yii\web\User::EVENT_AFTER_LOGIN, [new FirstAccessWizardComponent(), 'showWizard']);
//    }
}
