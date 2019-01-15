<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\chat
 * @category   CategoryName
 */

namespace lispa\amos\chat\console;

use lispa\amos\chat\AmosChat;
use lispa\amos\chat\models\Message;
use lispa\amos\chat\models\User;
use lispa\amos\admin\models\UserProfile;
use Yii;

/**
 * Class ChatController
 * @package lispa\amos\chat\console
 */
class ChatController extends \yii\console\Controller
{
    public function actionSendMails()
    {
        $data = Message::find()
            ->addSelect([
                'receiver_id',
                'sender_id',
                "CONCAT(senderUserProfile.nome, ' ', senderUserProfile.cognome) AS senderCompleteName",
                'receiverUser.email AS receiverEmail',
                'COUNT(*) AS msgCount'
            ])
            ->leftJoin(User::tableName() . ' AS receiverUser', 'receiver_id = receiverUser.id')
            ->leftJoin(UserProfile::tableName() . ' AS senderUserProfile', 'sender_id = senderUserProfile.id')
            ->andWhere([
                'is_new' => true,
                'is_deleted_by_receiver' => false
            ])
            ->groupBy(['receiver_id', 'sender_id'])
            ->asArray()->all();

        foreach ($data as $userData) {
            $subject = AmosChat::t('amoschat','Nuovo messaggio su ') . Yii::$app->name;
            Yii::$app->getMailer()
                ->compose(
                    [
                        'html' => '@vendor/lispa/amos-chat/src/mail/new-message/html',
                        'text' => '@vendor/lispa/amos-chat/src/mail/new-message/text'
                    ], [
                    'userData' => $userData,
                    'subject' => $subject,
                ])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setReplyTo([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($userData['receiverEmail'])
                ->setSubject($subject)
                ->send();
        }

        Yii::$app->end();
    }
}
