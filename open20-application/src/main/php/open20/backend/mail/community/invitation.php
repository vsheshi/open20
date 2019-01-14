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
                        <?php if (!empty($userName)): ?>
                             <p><?= \Yii::t('app', 'Gentile <strong>'. $userName . '</strong>, ')?></p>
                        <?php endif; ?>

<!--                            --><?php //AmosAdmin::tHtml('amosadmin', '#welcome_email_expire', [
//                                'passwordResetTokenExpire' =>  $passwordResetTokenExpire,
//                                'supportEmail' => Yii::$app->params['supportEmail']
//                            ]); ?>
                        <?php $community = $util->community; ?>
                        <p>
                        <?= AmosAdmin::t('amosadmin', "sei stato invitato a collaborare su PCDoc, la piattaforma di condivisione documentale sviluppata da Regione Lombardia, nell'Area di Lavoro/Stanza "). "<strong>".$community->name ."</strong>" ?>
                            <?php $link = $appLink . 'dashboard';
                            ?>
                            <br>
                            <br>

                            <?= AmosAdmin::t('amosadmin', "Al link sottostante potrai accedere, utilizzando le tue credenziali, direttamente nell’Area di Lavoro/Stanza in cui sei stato invitato a collaborare") ?>
                            <br>
                            <?= Html::beginTag('a', ['href' => $link]) ?>
                                <?= AmosAdmin::tHtml('amosadmin', 'Link di accesso alla piattaforma'); ?>
                            <?= Html::endTag('a'); ?>
                        </p>
                        <p>
                            <?= AmosAdmin::tHtml('amosadmin', 'Se il link non funziona puoi copiare e incollare questo link alternativo in una finestra del tuo browser di navigazione') ?>
                            <br>
                            <?= Html::beginTag('a', ['href' => $link]) ?>
                            <?= $link ?>
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