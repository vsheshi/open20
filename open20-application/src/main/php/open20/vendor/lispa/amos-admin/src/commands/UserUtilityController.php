<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\commands
 * @category   CategoryName
 */

namespace lispa\amos\admin\commands;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\core\user\User;
use yii\console\Controller;
use Yii;

class UserUtilityController  extends Controller
{


    /**
     *
     */
    public function actionBasicUserAssign(){
        /** @var AmosAdmin $admin */
        $admin = AmosAdmin::instance();
        $userClass = $admin->model('User');
        $users = $userClass::find()->all();
        /** @var User $user */
        foreach ($users as $user){
            $roles = Yii::$app->authManager->getAssignments($user->id);
            if(empty($roles)) {
                Yii::$app->getAuthManager()->assign(Yii::$app->getAuthManager()->getRole('BASIC_USER'), $user->id);
                $this->log ('Add BASIC_USER to : id'. $user->id . "  username: " . $user->username);
            }
        }
    }

    /**
     * @param $message
     */
    private function log($message){
        echo ($message ."\n");
    }
}