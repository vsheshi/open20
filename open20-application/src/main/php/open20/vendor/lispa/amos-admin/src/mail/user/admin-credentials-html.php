<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\mail\user
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\core\user\User $user
 * @var \lispa\amos\admin\models\UserProfile $profile
 */

$appLink = Yii::$app->urlManager->createAbsoluteUrl(['/']);
$appLinkPrivacy = Yii::$app->urlManager->createAbsoluteUrl(['/admin/user-profile/privacy']);
$appName = Yii::$app->name;

$this->title = AmosAdmin::t('amosadmin', 'Registrazione {appName}', ['appName' => $appName]);
$this->registerCssFile('http://fonts.googleapis.com/css?family=Roboto');

?>

<table style="line-height: 18px;" width=" 600" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td>
            <div class="corpo"
                 style="padding:10px;margin-bottom:10px;background-color:#ffffff;">

                <div class="sezione" style="overflow:hidden;color:#000000;">
                    <div class="testo">
                        <p style="margin-bottom: 20px;">
                            <span style="font-weight: bold;">
                                <?= AmosAdmin::tHtml('amosadmin', '#welcome_email_dear', [
                                    'name' => Html::encode($profile->nome),
                                    'surname' => Html::encode($profile->cognome)
                                ]); ?>
                                </span>
                            <br />
                            <?= AmosAdmin::tHtml('amosadmin', "#welcome_email_v2") . Yii::$app->name ?>.
                        </p>
                        <p style="margin-bottom: 20px;">
                            <?php
                            $seconds = Yii::$app->params['user.passwordResetTokenExpire'];

                            if($seconds >= 86400) {
                                $passwordResetTokenExpire = floor($seconds / (3600 * 24));
                                if($passwordResetTokenExpire == 1){
                                    $textDay = 'giorno';
                                } else {
                                    $textDay = 'giorni';
                                }
                            }else {
                                if(floor($seconds / 60)>=60){
                                    $textDay = chr(8);
                                    $passwordResetTokenExpire = sprintf("%d ore",floor($seconds / (60*60)));
                                } else {
                                    $textDay = 'minuti';
                                    $passwordResetTokenExpire = floor($seconds / 60);
                                }

                            }

                            $passwordResetTokenExpire = $passwordResetTokenExpire . ' ' . $textDay;
                            ?>
                            <?= AmosAdmin::tHtml('amosadmin', '#welcome_email_expire_v2', [
                                'passwordResetTokenExpire' =>  $passwordResetTokenExpire,
                                'supportEmail' => Yii::$app->params['supportEmail']
                            ]); ?>

                            <?php $link = $appLink . 'admin/security/insert-auth-data?token=' . $profile->user->password_reset_token;
                            if(!empty($community)) {
                                $link .= '&community_id='.$community->id;
                            }
                            ?>
                            <?= Html::beginTag('a', ['href' => $link]) ?>
                            <?= AmosAdmin::tHtml('amosadmin', "#welcome_email_link_v2") ?>
                            <?= Html::endTag('a'); ?>
                        </p>

                        <p style="margin-bottom: 20px;">
                            <?= AmosAdmin::tHtml('amosadmin', '#welcome_email_error_link') ?>
                            <?= AmosAdmin::tHtml('amosadmin', $link) ?>
                        </p>

                        <?php
                        /**
                         * @var \lispa\amos\socialauth\Module $social
                         */
                        $social = \Yii::$app->getModule('socialauth');
                        if($social && $social->enableRegister == true ): ?>
                            <p style="margin-bottom: 20px;">
                                <?= AmosAdmin::tHtml('amosadmin', '#welcome_email_social', [
                                    'platformName' => Yii::$app->name
                                ]) ?>
                            </p>
                        <?php endif; ?>
                        <p style="margin-bottom: 20px;">
                            <?= AmosAdmin::tHtml('amosadmin', '#welcome_email_change_data_v2') ?>
                        </p>

                        <p style="text-align: center">
                            <?php echo AmosAdmin::tHtml('amosadmin', '#auto_genearate_email_v1') ?>
                        </p>

                        <p style="text-align: left;margin-bottom: 20px">
                            <?= AmosAdmin::tHtml('amosadmin', '#welcome_email_thanks') ?>
                        </p>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>
