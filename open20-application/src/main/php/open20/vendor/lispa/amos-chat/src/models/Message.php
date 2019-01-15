<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\chat
 * @category   CategoryName
 */

namespace lispa\amos\chat\models;

use lispa\amos\chat\AmosChat;
use lispa\amos\chat\controllers\DefaultController;
use lispa\amos\emailmanager\AmosEmail;
use Yii;
use yii\log\Logger;

/**
 * Class Message
 * @package lispa\amos\chat\models
 */
class Message extends \lispa\amos\chat\models\base\Message
{
    public $userIdForward = [];
    /**
     * @inheritDoc
     */
    public function fields()
    {
        return [
            'messageId' => 'id',
            'senderId' => 'sender_id',
            'text',
            'date' => function ($model) {
                return static::formatDate($model['created_at'])[1];
            },
            'when' => function ($model) {
                return static::formatDate($model['created_at'])[0];
            },
        ];
    }

    /**
     * @param $value
     * @return array
     */
    public static function formatDate($value)
    {
        $today = date_create()->setTime(0, 0, 0);
        $date = date_create($value)->setTime(0, 0, 0);
        if ($today == $date) {
            $label = AmosChat::tHtml('amoschat', 'Oggi');
        } elseif ($today->getTimestamp() - $date->getTimestamp() == 24 * 60 * 60) {
            $label = AmosChat::tHtml('amoschat', 'Ieri');
        } elseif ($today->format('W') == $date->format('W') && $today->format('Y') == $date->format('Y')) {
            $label = Yii::$app->formatter->asDate($value, 'php:l');
        } elseif ($today->format('Y') == $date->format('Y')) {
            $label = Yii::$app->formatter->asDate($value, 'php:d F');
        } else {
            $label = Yii::$app->formatter->asDate($value, 'medium');
        }
        $formatted = Yii::$app->formatter->asTime($value, 'short');
        return [$label, $formatted];
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        try {
            /** @var AmosChat $module */
            $module = Yii::$app->controller->module;
            if (!empty($module) && isset($module->immediateNotificationForce) && ($module->immediateNotificationForce == true)) {
                $from = '';
                if (isset(Yii::$app->params['email-assistenza'])) {
                    //use default platform email assistance
                    $from = Yii::$app->params['email-assistenza'];
                }
                $sender = User::findOne($this->sender_id);
                $senderProfile = $sender->profile;
                $subject = AmosChat::t('amoschat', 'You received a new private message');
                /** @var DefaultController $controller */
                $controller = Yii::$app->controller;
                $text = $controller->renderMailPartial('email', [
                    'contactProfile' => $senderProfile
                ], $this->receiver_id);

                $mailModule = Yii::$app->getModule('email');
                if (!is_null($mailModule)) {
                    /** @var \lispa\amos\emailmanager\AmosEmail $mailModule */
                    $mailModule->send(
                        (!empty($module->defaultEmailSender)? $module->defaultEmailSender : (!empty($from) ? $from : $sender->email)),
                        User::findOne($this->receiver_id)->email,
                        $subject,
                        $text
                    );
                }
            }
        } catch (\Exception $exception) {
            Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
        }
        parent::afterSave($insert, $changedAttributes);
    }

}