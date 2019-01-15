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
use lispa\amos\core\utilities\Email;
use Yii;
use yii\base\Model;

/**
 * Class ProfileReactivationForm
 * @package lispa\amos\admin\models
 */
class ProfileReactivationForm extends Model
{
    public $email;
    public $message;
    public $username;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'required', 'message' => AmosAdmin::t('amosadmin', "#reactivate_profile_email_alert")],
            [['message'], 'required', 'message' => AmosAdmin::t('amosadmin', "#reactivate_profile_message_alert")],
            [['email', 'message'], 'string'],
            [['email', 'message'], 'safe'],
            [['email'], 'email'],
            [['email'], 'validateEmail']
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'message' => AmosAdmin::t('amosadmin', 'Message')
        ];
    }
    
    /**
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateEmail($attribute, $params)
    {
        $userByEmail = User::findOne(['email' => $this->email]);
        if (is_null($userByEmail)) {
            $this->addError($attribute, AmosAdmin::t('amosadmin', 'Email non presente in piattaforma.'));
        }
    }
    
    /**
     * @return bool
     */
    public function sendMail()
    {
        // Default email values
        $userToReactivate = User::findOne(['email' => $this->email]);
        /*$from = (!is_null($userToReactivate) ? $userToReactivate->email : '');*/
        $from = '';
        if (isset(Yii::$app->params['email-assistenza'])) {
            //use default platform email assistance
            $from = Yii::$app->params['email-assistenza'];
        }
        if (!$from) {
            return false;
        }
        
        /** @var UserProfile $userProfile */
        $userProfile = $userToReactivate->getProfile();
        $adminUserIds = Yii::$app->getAuthManager()->getUserIdsByRole('ADMIN');
        $to = [];
        foreach ($adminUserIds as $adminUserId) {
            $user = User::findOne(['id' => $adminUserId]);
            if (!is_null($user)) {
                $to[] = $user->email;
            }
        }
        $subject = null;
        $text = null;
        $files = [];
        $bcc = [];
        $params = [];
        $priority = 0;
        $use_queue = false;
        
        // Populate SUBJECT
        $subject = AmosAdmin::t('amosadmin', 'Richiesta di riattivazione per l\'utente "' . $userProfile->getNomeCognome() . '"');
        
        // Populate TEXT
        $text = '<h2>' . AmosAdmin::t('amosadmin', $userProfile->getNomeCognome() . ' ha richiesto la riattivazione del suo profilo.') . '</h2><br>';
        $text .= AmosAdmin::t('amosadmin', 'Ecco il testo della richiesta') . ':<br><br>';
        $text .= strip_tags($this->message);
        
        // SEND EMAIL
        $ok = Email::sendMail(
            $from,
            $to,
            $subject,
            $text,
            $files,
            $bcc,
            $params,
            $priority,
            $use_queue
        );
        
        return $ok;
    }
}
