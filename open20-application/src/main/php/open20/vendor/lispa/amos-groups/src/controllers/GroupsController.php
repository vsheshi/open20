<?php

namespace lispa\amos\groups\controllers;

use lispa\amos\groups\controllers\base\GroupsController as BaseGroupsController;
use lispa\amos\groups\models\Groups;
use lispa\amos\groups\models\GroupsMailer;
use lispa\amos\groups\Module;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;
use yii\helpers\BaseArrayHelper;

/**
 * This is the class for controller "GroupsController".
 */
class GroupsController extends BaseGroupsController
{

    public function behaviors()
    {
        return BaseArrayHelper::merge(parent::behaviors(),
                [
                'access' => [
                    'class' => AccessControl::className(),
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'send-email-to-group'
                            ],
                            'roles' => ['@']
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post', 'get']
                    ]
                ]
        ]);
    }

    /**
     * @param null $idGroup
     * @return string
     */
    public function actionSendEmailToGroup($idGroup = null)
    {
        $mailer          = new GroupsMailer();
        $modelGroup = new Groups();
        if($idGroup){
            $mailer->idGroup = $idGroup;
            $modelGroup = parent::findModel($idGroup);
        }

        if (\Yii::$app->request->post() && $mailer->load(\Yii::$app->request->post()) && $mailer->validate()) {

            /** @var  $model Groups */
            if (GroupsMailer::sendEmail($mailer->idGroup, $mailer->subject, $mailer->text)) {
                \Yii::$app->session->addFlash('success', Module::t('amosgroups', 'Email sent'));
                //$this->redirect(['index']);
            } else {
                \Yii::$app->session->addFlash('danger', Module::t('amosgroups', 'Email not sent'));
            }
        }
        return $this->render('_new_email', ['model' => $mailer, 'modelGroup' => $modelGroup]);
    }
}