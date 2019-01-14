<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$appLink = Yii::$app->urlManager->createAbsoluteUrl(['/']);
$appLinkPrivacy = Yii::$app->urlManager->createAbsoluteUrl(['/admin/user-profile/privacy']);
$appName = Yii::$app->name;

$this->title = Yii::t('piattaformaopeninnovation', 'Nuovo iscritto nella piattaforma {appName}', ['appName' => $appName]);
?>

<table width="600" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td>
            <div class="corpo" style="background-color:#fff;padding:50px;">               
                <div class="sezione titolo" style="overflow:hidden;color:#000000;">
                    <h2 style="padding:5px 0;	margin:0;">Nuovo iscritto</h2>
                </div>
                <div class="sezione" style="overflow:hidden;color:#000000;">
                    <div class="testo">
                    Un nuovo utente si &eacute; iscritto alla piattaforma FITSTIC!
                    <br />                    
                    Ecco i dati riepilogativi del nuovo utente: 
                    <br />
                        <p><?=
                            Yii::t('piattaformaopeninnovation', '<b>Nome:</b>: {nome}', [
                                'nome' => Html::encode($profile->nome)]);
                            ?>
                         </p>
                         <p>
                            <?=
                            Yii::t('piattaformaopeninnovation', '<b>Cognome:</b> {cognome}', [
                                'cognome' => Html::encode($profile->cognome)]);
                            ?>
                         </p>
                         <p>
                            <?=
                            Yii::t('piattaformaopeninnovation', '<b>Cofice Fiscale:</b> {cf}', [
                                'cf' => Html::encode($profile->codice_fiscale)]);
                            ?>
                         </p>
                         <p>
                            <?=
                            Yii::t('piattaformaopeninnovation', '<b>Data di Nascita:</b> {nascita_data}', [
                                'nascita_data' => Html::encode($profile->nascita_data)]);
                            ?>
                         </p>
                         <p>
                            <?=
                            Yii::t('piattaformaopeninnovation', '<b>Sesso:</b> {sesso}', [
                                'sesso' => Html::encode($profile->sesso)]);
                            ?>
                         </p>
                         <p>                          
                            <?=
                            Yii::t('piattaformaopeninnovation', '<b>Email:</b> {email}', [
                                'email' => Html::encode($user->email)]);
                            ?>
                         </p>
                         <p>                                                                                   
                            <?=
                            Yii::t('piattaformaopeninnovation', '<b>Nazione di nascita:</b> {nazionedinascita}', [
                                'nazionedinascita' => Html::encode($profile->nascitaNazioni->nome)]);
                            ?>                                                   
                        </p>                                                
                    </div>
                </div>
            </div>
        </td>
    </tr>

    <tr>
        <td>
            <div id="footer"
                 style="padding:20px 50px;background-color:#ffffff;">
                     <?=
                     Yii::t('piattaformaopeninnovation', '{appName} - {appLink}', [
                         'appName' => $appName,
                         'appLink' => Html::a(Html::encode($appLink), $appLink, [
                             'style' => "text-decoration:underline;"
                         ]),
                     ])
                     ?>


            </div>
        </td>
    </tr>

    <tr>
        <td>
            <div class="sezione bottom" style="padding:15px 50px;color:#233C32;font-size:11px;">
                <?=
                Yii::t('piattaformaopeninnovation', 'Questo messaggio è stato inviato automaticamente dalla piattaforma {appName}.', [
                    'appName' => $appName,
                ])
                ?>
            </div>
        </td>
    </tr>
</table>