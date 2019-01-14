<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$appLink = Yii::$app->urlManager->createAbsoluteUrl(['/']);
$appLinkPrivacy = Yii::$app->urlManager->createAbsoluteUrl(['/admin/user-profile/privacy']);
$appName = Yii::$app->name;
?>

<?= "\n"; ?>
<?= "Carissimo iscritto al portale FITSTIC,"; ?>
<?= "\n"; ?>
<?= "per finalizzare l'iscrizione alle selezioni dei corsi"; ?>
<?php

if (!empty($corsi)) {
    foreach ($corsi as $corso) {
        $Corso = backend\modules\corsi\models\Corsi::findOne($corso);
        if ($Corso) {
            ?>
            <?= "\n\t- $Corso->titolo - $Corso->nome"; ?>
        <?php

        }
    }
}
?>
<?= "\n"; ?>
<?= "le inviamo in allegato i moduli da compilare, firmare e spedire a: 
\nFondazione Istituto Tecnico Superiore Tecnologie Industrie Creative - FITSTIC - Piazza C. Macrelli, 100 - 47521 Cesena (FC)."; ?>
<?= "\n"; ?>
<?= "Alleghiamo anche l'avviso che spiega nel dettaglio il processo di selezione, che le consigliamo di leggere bene."; ?>
<?= "\n"; ?>
<?= "Siamo sempre a disposizione per chiarimenti,"; ?>
<?= "\n"; ?>
<?= "Cordiali Saluti."; ?>
<?= "\n"; ?>


<?= "\n"; ?>
<?= "Congratulazioni!"; ?>
<?= "\n"; ?>
<?= "Ora sei iscritto al portale FITSTIC!"; ?>
<?= "\n"; ?>
<?= "Ecco i tuoi dati riepilogativi: "; ?>
<?=

Yii::t('piattaformaopeninnovation', 'Nuovo iscritto nella piattaforma {appName}', [
    'appName' => $appName
        ]
)
?>
<?= "\n"; ?>
<?=

Yii::t('piattaformaopeninnovation', 'Nome: {nome}', [
    'nome' => Html::encode($profile->nome)]);
?>
<?= "\n"; ?>
<?=

Yii::t('piattaformaopeninnovation', 'Cognome: {cognome}', [
    'cognome' => Html::encode($profile->cognome)]);
?>
<?= "\n"; ?>
<?=

Yii::t('piattaformaopeninnovation', 'Cofice Fiscale: {cf}', [
    'cf' => Html::encode($profile->codice_fiscale)]);
?>
<?= "\n"; ?>
<?=

Yii::t('piattaformaopeninnovation', 'Data di nascita: {nascita_data}', [
    'nascita_data' => Html::encode($profile->nascita_data)]);
?>
<?= "\n"; ?>
<?=

Yii::t('piattaformaopeninnovation', 'Sesso: {sesso}', [
    'sesso' => Html::encode($profile->sesso)]);
?>
<?= "\n"; ?>
<?=

Yii::t('piattaformaopeninnovation', 'Email: {email}', [
    'email' => Html::encode($user->email)]);
?>
<?= "\n"; ?>
<?=

Yii::t('piattaformaopeninnovation', 'Nazione di nascita: {nazionedinascita}', [
    'nazionedinascita' => Html::encode($profile->nascitaNazioni->nome)]);
?>

<?= "\n"; ?>
<?= "\n"; ?>
<?=

Yii::t('piattaformaopeninnovation', 'Questo messaggio è stato inviato automaticamente dalla piattaforma {appName}.', [
    'appName' => $appName,
])
?>
<?= "\n"; ?>
<?= "\n"; ?>
<?= $appLink; ?>


