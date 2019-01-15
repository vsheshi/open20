<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comments\base
 * @category   CategoryName
 */

namespace lispa\amos\comments\base;

use lispa\amos\comments\AmosComments;
use lispa\amos\comments\models\Comment;
use lispa\amos\comments\models\CommentReply;
use lispa\amos\core\interfaces\ModelLabelsInterface;
use lispa\amos\core\record\Record;
use lispa\amos\core\user\User;
use lispa\amos\core\utilities\Email;
use Yii;
use yii\base\Object;

/**
 * Class PartecipantsNotification
 * @package lispa\amos\comments\base
 */
class PartecipantsNotification extends Object
{
    /**
     * @param $content_id
     */
    public function partecipantAlert($comment)
    {
        $users = [];
        $model_reply = null;

        /**@var $$module AmosComments */
        $module = Yii::$app->getModule(AmosComments::getModuleName());
        if (!empty($module)) {
            if (!$module->enableMailsNotification) {
                return;
            }
        }

        $model = $comment;
        if ($comment->className() ==  'lispa\amos\comments\models\CommentReply') {
            $model = $comment->comment;
            $model_reply = $comment;
        }

        /** @var \lispa\amos\core\record\Record $contextModelClassName */
        $contextModelClassName = $model->context;
        /** @var \lispa\amos\core\record\Record $contextModel */
        $contextModel = $contextModelClassName::findOne($model->context_id);
        $users[$contextModel->created_by] = $contextModel->created_by;

        $comments = Comment::find()->where('context_id = ' . $contextModel->id)
            ->groupBy(['created_by'])
            ->all();
        foreach ($comments as $comment) {
            $users[$comment->created_by] = $comment->created_by;
            $commnetReplies = CommentReply::find()->where('comment_id = ' . $comment->id)
                ->groupBy(['created_by'])
                ->all();
            foreach ($commnetReplies as $reply) {
                $users[$reply->created_by] = $reply->created_by;
            }
        }

        $this->sendEmail($users, $contextModel, $model, $model_reply);
        if($model_reply && !in_array($model->created_by, $users)) {
            $this->sendEmail([$model->created_by], $contextModel, $model, $model_reply);
        }
    }


    /**
     * @param array $userIds
     * @param $model
     */
    private function sendEmail(array $userIds, $contextModel, $model, $model_reply = null)
    {
        try {
            foreach ($userIds as $id) {
                $user = User::findOne($id);
                $subject = $this->getSubject($contextModel);
                $message = $this->renderEmail($contextModel, $model, $model_reply, $user);
                if (!is_null($user)) {
                    $email = new Email();
                    $from = '';
                    if (isset(Yii::$app->params['email-assistenza'])) {
                        //use default platform email assistance
                        $from = Yii::$app->params['email-assistenza'];
                    }
                    $email->sendMail($from, [$user->email], $subject, $message);
                }
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param Record $contextModel
     * @return string
     */
    private function getSubject(Record $contextModel)
    {
        $controller = \Yii::$app->controller;
        $subject = $controller->renderMailPartial('email' . DIRECTORY_SEPARATOR . 'content_subject', ['contextModel' => $contextModel]);
        return $subject;
    }

    /**
     * @param $contextModel
     * @param $model
     * @param $model_reply
     * @param null $user
     * @return string
     */
    private function renderEmail($contextModel, $model, $model_reply , $user = null)
    {
        $mail = '';
        try {
            if ($model != null) {
                $mail .= $this->renderContentTitle($contextModel, $model, $model_reply);
                $mail .= $this->renderContent($contextModel, $model, $model_reply, $user);
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $mail;
    }

    /**
     * @param Record $contextModel
     * @param Record $model
     * @param $model_reply
     * @param null $user
     * @return mixed
     */
    private function renderContent(Record $contextModel, Record $model, $model_reply, $user = null)
    {
        $commentModule = \Yii::$app->getModule(AmosComments::getModuleName());
        $content = 'email' . DIRECTORY_SEPARATOR . 'content';
        if($commentModule) {
            if(is_array($commentModule->htmlMailContent)){
                if(!empty($commentModule->htmlMailContent[$contextModel->className()])){
                    $content = $commentModule->htmlMailContent[$contextModel->className()];
                }
            }
            else {
                $content = $commentModule->htmlMailContent;
            }
        }

        $controller = \Yii::$app->controller;
        $ris = $controller->renderMailPartial($content, [
            'model' => $model,
            'contextModel' => $contextModel,
            'model_reply' => $model_reply,
            'user' => $user
        ]);
        return $ris;
    }

    /**
     * @param ModelLabelsInterface $model
     * @param Comment $modeComment
     * @param CommentReply $model_reply
     * @return string
     */
    private function renderContentTitle(ModelLabelsInterface $model, $modelComment, $model_reply)
    {
        $commentModule = \Yii::$app->getModule(AmosComments::getModuleName());
        if($commentModule) {
            $content = $commentModule->htmlMailContentTitle;
        }
        else {
            $content = 'email' . DIRECTORY_SEPARATOR . 'content_title';
        }

        $controller = \Yii::$app->controller;
        $ris = $controller->renderMailPartial($content, [
            'model' => $model,
            'modelComment' => $modelComment,
            'model_reply' => $model_reply
        ]);
        return $ris;
    }
}
