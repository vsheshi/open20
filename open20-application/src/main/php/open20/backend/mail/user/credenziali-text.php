<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin
 * @category   CategoryName
 */

use yii\helpers\Html;
use lispa\amos\admin\AmosAdmin;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$appLink = Yii::$app->urlManager->createAbsoluteUrl(['/']);
$appLinkPrivacy = Yii::$app->urlManager->createAbsoluteUrl(['/admin/user-profile/privacy']);
$appName = Yii::$app->name;
$sessoBenvenuto = AmosAdmin::t('amosadmin', 'Benvenuto');
if ($profile->sesso == 'Femmina') {
    $sessoBenvenuto = AmosAdmin::t('amosadmin', 'Benvenuta');
}
?>
<?= AmosAdmin::tHtml('amosadmin', 'Gentile {nome} {cognome},', [
    'nome' => Html::encode($profile->nome),
    'cognome' => Html::encode($profile->cognome)]);
?>
<?= "\n"; ?>
<?= AmosAdmin::tHtml('amosadmin', 'la presente per comunicarle, ai sensi dell’art. 6 dell’AVVISO PUBBLICO “Presentazione domande volte all’inserimento nel Registro dei formatori del Sistema della IeFP della Regione Emilia Romagna, per l’eventuale conferimento di incarichi non a carattere subordinato”, che la sua domanda di inserimento nel Registro dei Formatori risulta formalmente corretta e pertanto accoglibile.\n\nDi seguito troverà le credenziali per accedere alla Piattaforma collaborativa dei Formatori IeFP, dove le verrà richiesto, come primo accesso, il consenso a pubblicare il suo CV ed i suoi dati sulla pagina dedicata.\n\nGrazie della collaborazione.\n\n') ?>
<?= "\n"; ?>
<?= Html::beginTag('a', ['href' => $appLink . 'site/insert-auth-data?token=' . $profile->user->password_reset_token]) ?>
                            <?= AmosAdmin::t('amosadmin', 'Link di accesso alla piattaforma'); ?>
                            <?= Html::endTag('a'); ?>
<?= "\n"; ?>
<?= AmosAdmin::t('amosadmin', 'Se il link non funziona copia e incolla il seguente in una finestra del tuo browser di navigazione') ?>
                            <?= AmosAdmin::t('amosadmin', $appLink . 'site/insert-auth-data?token=' . $profile->user->password_reset_token) ?>
		<?= "\n"; ?>
		<?= "\n"; ?>	
		
<?=
                AmosAdmin::t('amosadmin', '{nome} {cognome}, questo messaggio ti è stato inviato automaticamente dalla piattaforma {appName}, a cui sei registrato con l\'indirizzo email {email}.', [
					'appName' => $appName,
                    'nome' => $profile->nome,
                    'cognome' => $profile->cognome,
                    'email' => $profile->user->email,
                ])
                ?>
<?= "\n"; ?>
<?= "\n"; ?>
<?= $appLink; ?>


