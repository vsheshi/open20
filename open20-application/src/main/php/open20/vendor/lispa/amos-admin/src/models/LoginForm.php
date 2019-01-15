<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\models
 * @category   CategoryName
 */

namespace lispa\amos\admin\models;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\core\user\User;
use Yii;
use yii\base\Model;
use lispa\amos\admin\models\UserLockout;

/**
 * Class LoginForm
 * @package lispa\amos\admin\models
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $ruolo;
    private $_user;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            ['ruolo', 'required', 'when' => function ($model) {
                return (isset(\Yii::$app->params['template-amos']) && \Yii::$app->params['template-amos']);
            }],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['ruolo', 'safe']
        ];
    }
    
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, AmosAdmin::t('amosadmin', 'Incorrect username or password.'));
            }
        }
    }
    
    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->checkUserAttemptsExceed($this->getUser())) {
            return false;
        } else {
            if ($this->validate()) {
                //Reset attempts
                $this->resetLockoutAttempts();

                return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            } else {
                $this->tickAttemptUser($this->getUser());

                return false;
            }
        }
    }
    
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        $userModel = AmosAdmin::instance()->createModel('User');

        if ($this->_user === null) {
            $this->_user = $userModel->findByUsername($this->username);
        }
        return $this->_user;
    }

    private function resetLockoutAttempts()
    {
        $userIp = $userIp = Yii::$app->request->getUserIP();;
        if (!empty($userIp)) {
            // Reset counter of attempts
            /** @var UserLockout $ul */
            $ul = UserLockout::find()->andWhere(['ip' => $userIp])->one();
            if (!empty($ul)) {
                $ul->attempts = 0;
                $ul->save(false);
            }
        }
    }

    /**
     * @param $user \lispa\amos\core\user\User
     */
    private function tickAttemptUser($user)
    {
        $userIp = $userIp = Yii::$app->request->getUserIP();;

        /** @var UserLockout $ul */
        $ul = UserLockout::find()->andWhere(['ip' => $userIp])->one();
        if (!empty($ul)) {
            $ul->attempts = (integer)$ul->attempts + 1;
        } else {
            $ul = new UserLockout;
            $ul->ip = $userIp;
            $ul->attempts = 1;
        }
        $ul->save(false);
    }

    /**
     * @param $user \lispa\amos\core\user\User
     */
    private function checkUserAttemptsExceed($user)
    {
        $userIp = $userIp = Yii::$app->request->getUserIP();

        if (empty($userIp)) {
            return false;
        }

        if (isset(Yii::$app->params['access-attempts-lockout'])) {
            $numberAttempts = (integer)Yii::$app->params['access-attempts-lockout'];
        } else {
            $numberAttempts = (integer)UserLockout::ACCESSS_ATTEMPTS_LOCKOUT_DEFAULT;
        }

        // if access-attempts-lockout is 0 no control
        if ($numberAttempts == 0){
            return false;
        }

        if (isset(Yii::$app->params['access-time-lockout'])) {
            $hoursToStop = (integer)Yii::$app->params['access-time-lockout'];
        } else {
            $hoursToStop = (integer)UserLockout::ACCESSS_TIME_LOCKOUT_DEFAULT;
        }

        /** @var UserLockout $ul */
        $ul = UserLockout::find()->andWhere(['ip' => $userIp])->one();

        if (!empty($ul) && ($ul->attempts >= $numberAttempts)) {
            // if hours to stop is 0, never check the time elapsed
            if ($hoursToStop == 0) {
                Yii::$app->getSession()->addFlash('danger', 'Maximum number of attempts passed.');
                return true;
            }

            // check if last try is over the time of $hoursToStop
            $nowDateTimeString = date("D M d, Y G:i");
            $nowDateTime = strtotime($nowDateTimeString);
            $updateDateTimeSting = $ul->updated_at;
            $updateDateTime = strtotime($updateDateTimeSting);
            $deltaHours = (($nowDateTime - $updateDateTime) / 3600);

            if ($deltaHours >= $hoursToStop) {
                $ul->attempts = 0;
                $ul->save();
                return false;
            } else {
                $time = $hoursToStop - $deltaHours;

                Yii::$app->getSession()->addFlash('danger', 'Maximum number of attempts passed. 
                    Try again in ' . $this->convertTime($time) . '.');

                return true;
            }
        } else {
            return false;
        }

    }

    private function convertTime($dec)
    {
        $seconds = ($dec * 3600);
        $hours = floor($dec);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        return (($hours > 0)? $this->lz($hours)." ore ": '')
            . (($minutes > 0)? $this->lz($minutes) . ' minuti': '1 minuto') ;
    }

    private function lz($num)
    {
        return (strlen($num) < 2) ? "0{$num}" : $num;
    }

    /**
     * @param $user \lispa\amos\core\user\User
     * @return int|null
     */
    private function numbersOfAttemptsRemainig($user = false)
    {
        $userIp = Yii::$app->request->getUserIP();

        if (empty($userIp)) {
            return null;
        }
        if (isset(Yii::$app->params['access-attempts-lockout'])) {
            $numberAttempts = (integer)Yii::$app->params['access-attempts-lockout'];
        } else {
            $numberAttempts = (integer)UserLockout::ACCESSS_ATTEMPTS_LOCKOUT_DEFAULT;
        }
        if ($numberAttempts == 0) {
            return null;
        }

        /** @var UserLockout $ul */
        $ul = UserLockout::find()->andWhere(['ip' => $userIp])->one();
        if (!empty($ul)) {
            $remaining = $numberAttempts - (integer)$ul->attempts;
            return $remaining;
        }
        return null;
    }
}
