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
                                <?= AmosAdmin::tHtml('app', 'Gentile {name} {surname}', [
                                    'name' => Html::encode($profile->nome),
                                    'surname' => Html::encode($profile->cognome)
                                ]).','; ?>
                                </span>
                            <br />
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
<!--                            --><?php //AmosAdmin::tHtml('amosadmin', '#welcome_email_expire', [
//                                'passwordResetTokenExpire' =>  $passwordResetTokenExpire,
//                                'supportEmail' => Yii::$app->params['supportEmail']
//                            ]); ?>

                            <?php if(!empty($community)){ ?>
                                <?php echo AmosAdmin::t('amosadmin', "sei stato invitato a collaborare su PCDoc, la piattaforma di condivisione documentale sviluppata da Regione Lombardia, nell'Area di Lavoro/Stanza "). "<strong>" . $community->name . "</strong>" ?>
                            <?php } else { ?>
                                <?= AmosAdmin::t('amosadmin', "sei stato invitato a collaborare su PCDoc, la piattaforma di condivisione documentale sviluppata da Regione Lombardia.") ?>
                            <?php  } ?>

                            <?php $link = $appLink . 'admin/security/insert-auth-data?token=' . $profile->user->password_reset_token;
                            if(!empty($community)) {
                                //$link .= '&community_id='.$community->id;
                            }
                            ?>
                            <br>
                            <br>

                            <?php if(!empty($community)){ ?>
                                <?php echo AmosAdmin::t('amosadmin', "Al link sottostante potrai accedere alla piattaforma ed entrare direttamente nell’Area di Lavoro/Stanza in cui sei stato invitato a collaborare.
                                    <br>Prima di accedere ti verrà richiesto di impostare le tue credenziali di accesso") ?>
                            <?php } else { ?>
                                <?= AmosAdmin::t('amosadmin', "Al link sottostante potrai accedere alla piattaforma e impostare le tue credenziali di accesso") ?>
                            <?php  } ?>

                            <br>
                            <?= Html::beginTag('a', ['href' => $link]) ?>
                                <?= AmosAdmin::tHtml('amosadmin', 'Link di accesso alla piattaforma'); ?>
                            <?= Html::endTag('a'); ?>
                        </p>
                        <p>
                            <?= AmosAdmin::tHtml('amosadmin', 'Se il link non funziona puoi copiare e incollare questo link alternativo in una finestra del tuo browser di navigazione') ?>
                            <br>
                            <?= Html::beginTag('a', ['href' => $link]) ?>
                            <?= AmosAdmin::tHtml('amosadmin', $link) ?>
                            <?= Html::endTag('a'); ?>
                        </p>
                        <p>
                            <?= AmosAdmin::tHtml('amosadmin', 'Hai ricevuto questo messaggio perché un utente già registrato ti ha invitato a collaborare.') ?>
                            <br>
                            <?= Html::beginTag('a', ['href' => $appLink . 'site/privacy']) ?>
                            <?= AmosAdmin::tHtml('amosadmin', "Visualizza l’informativa sul trattamento dei dati e sulla privacy"); ?>
                            <?= Html::endTag('a'); ?>
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
                        <p style="text-align: center">
                            <?php echo AmosAdmin::tHtml('amosadmin', '**Questa mail è generata automaticamente, ti preghiamo di non rispondere**') ?>
                        </p>
                        <p style="margin-bottom: 20px">
                            <?= AmosAdmin::tHtml('amosadmin', 'Grazie, <br> lo staff PCDoc') ?>
                        </p>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>