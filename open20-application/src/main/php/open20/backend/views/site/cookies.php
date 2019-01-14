<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    backend\views\site
 * @category   CategoryName
 */

use lispa\amos\core\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

$this->title = Yii::t('amosapp', 'Cookies');

?>

<section>
    <h1 class="bold"><?= Yii::t('amosapp', "Informativa sull'uso dei cookie") ?></h1>
    <h3 class="bold"><?= Yii::t('amosapp', 'Cosa sono i cookie?') ?></h3>
    <p>
        <?= Yii::t('amosapp', "I cookie sono piccoli file di testo che vengono inviati dal sito web visitato dall’utente sul dispositivo dell'utente (solitamente al browser), dove vengono memorizzati in modo da poter riconoscere tale dispositivo alla successiva visita. Ad ogni visita successiva, infatti, i cookie sono reinviati dal dispositivo dell’utente al sito.") ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', "I cookie possono essere istallati, però, non solo dallo stesso gestore del sito visitato dall'utente (") ?>
        <strong><?= Yii::t('amosapp', 'cookie di prima parte') ?></strong><?= Yii::t('amosapp', "), ma anche da un sito diverso che istalla cookie per il tramite del primo sito (") ?>
        <strong><?= Yii::t('amosapp', 'cookie di terze parti') ?></strong><?= Yii::t('amosapp', ") ed è in grado di riconoscerli. Questo accade perché sul sito visitato possono essere presenti elementi (immagini, mappe, suoni, link a pagine web di altri domini, etc) che risiedono su server diversi da quello del sito visitato. Ai sensi di quanto indicato nel Provvedimento del Garante della Privacy del 8 Maggio 2014 ") ?>
        <em><?= Yii::t('amosapp', "\"Individuazione delle modalità semplificate per l'informativa e l'acquisizione del consenso per l'uso dei cookie\"") ?></em><?= Yii::t('amosapp', " (di seguito, \"") ?>
        <strong><?= Yii::t('amosapp', 'Provvedimento') ?></strong><?= Yii::t('amosapp', "\"), per i cookie istallati da terze parti, gli obblighi di informativa e, se ne ricorrono i presupposti, di consenso gravano sulle terze parti; il titolare del sito, in qualità di intermediario tecnico fra le terze parti e gli utenti, è tenuto ad inserire nell’informativa estesa i link aggiornati alle informative e i moduli di consenso delle terze parti. In base alla finalità, i cookie si distinguono in cookie tecnici ed in cookie di profilazione.") ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', "In base alla finalità, i cookie si distinguono in ") ?><strong><?= Yii::t('amosapp', 'cookie tecnici') ?></strong><?= Yii::t('amosapp', " ed in ") ?>
        <strong><?= Yii::t('amosapp', 'cookie di profilazione') ?></strong>.
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', "I cookie tecnici sono istallati al solo fine di \"") ?>
        <em><?= Yii::t('amosapp', "effettuare la trasmissione di una comunicazione su una rete di comunicazione elettronica, o nella misura strettamente necessaria al fornitore di un servizio della società dell'informazione esplicitamente richiesto dall'abbonato o dall'utente a erogare tale servizio") ?></em><?= Yii::t('amosapp', " (cfr. art. 122, co. 1, del D.Lgs. 196/2003 - cd. Codice Privacy - così come modificato dal d.lgs. 69/2012). Sono solitamente utilizzati per consentire una navigazione efficiente tra le pagine, memorizzare le preferenze degli utenti, memorizzare informazioni su specifiche configurazioni degli utenti, effettuare l’autenticazione degli utenti, etc. I cookie tecnici possono essere suddivisi in ") ?>
        <em><?= Yii::t('amosapp', 'cookie di navigazione') ?></em>
        <?= Yii::t('amosapp', ", utilizzati al fine di registrare dati utili alla normale navigazione e fruizione del sito web sul computer dell'utente (permettendo, ad esempio, di ricordare la dimensione preferita della pagina in un elenco) e ") ?>
        <em><?= Yii::t('amosapp', 'cookie funzionali') ?></em>
        <?= Yii::t('amosapp', ", che consentono al sito web di ricordare le scelte effettuate dall’utente al fine di ottimizzarne le funzionalità (ad esempio, i cookie funzionali consentono al sito di ricordare le impostazioni specifiche di un utente, come la selezione del paese e, se impostato, lo stato di accesso permanente). Alcuni di questi cookie (detti essenziali o strictly necessary) abilitano funzioni senza le quali non sarebbe possibile effettuare alcune operazioni. Ai sensi del suddetto art. 122, co. 1, Codice Privacy, l'utilizzo dei cookie tecnici non richiede il consenso degli utenti.") ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', "Ai cookie tecnici sono assimilati (e, pertanto, come precisato nel Provvedimento, per la relativa istallazione, non è richiesto il consenso degli utenti, né gli ulteriori adempimenti normativi) i ") ?>
        <strong><?= Yii::t('amosapp', "cookie cd. analytics") ?></strong>
        <?= Yii::t('amosapp', " se realizzati e utilizzati direttamente dal gestore del sito prima parte (senza, dunque, l'intervento di soggetti terzi) ai fini di ottimizzazione dello stesso per raccogliere informazioni aggregate sul numero degli utenti e su come questi visitano il sito stesso, nonché i cookie analitici realizzati e messi a disposizione da terze parti ed utilizzati dal sito prima parte per meri fini statistici, qualora vengano, fra l’altro, adottati strumenti idonei a ridurre il potere identificativo dei cookie analitici che utilizzano (ad esempio, mediante il mascheramento di porzioni significative dell'indirizzo IP).") ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', 'I') ?><strong><?= Yii::t('amosapp', "cookie di profilazione") ?></strong>
        <?= Yii::t('amosapp', "servono a tracciare la navigazione dell’utente, analizzare il suo comportamento ai fini marketing e creare profili sui suoi gusti, abitudini, scelte, etc. in modo da trasmettere messaggi pubblicitari mirati in relazione agli interessi dell’utente ed in linea con le preferenze da questi manifestati nella navigazione on line. Tali cookie possono essere istallati sul terminale dell’utente solo se questo abbia espresso il proprio consenso con le modalità indicate nel Provvedimento.") ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', 'In base alla loro durata, i cookie si distinguono in ') ?><strong><?= Yii::t('amosapp', "persistenti") ?></strong>
        <?= Yii::t('amosapp', ", che rimangono memorizzati, fino alla loro scadenza, sul dispositivo dell’utente, salva rimozione da parte di quest’ultimo, e ") ?>
        <strong><?= Yii::t('amosapp', " di sessione") ?></strong>
        <?= Yii::t('amosapp', ", che non vengono memorizzati in modo persistente sul dispositivo dell’utente e svaniscono con la chiusura del browser.") ?>
    </p>
    <br>
    <h4 class="bold"><?= Yii::t('amosapp', 'Quali cookie sono utilizzati nel portale della Piattaforma di Condivisione Documentale?') ?></h4>
    <p>
        <ins><?= Yii::t('amosapp', "Cookie tecnici") ?></ins>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', 'Il presente sito fa uso di ') ?>
        <strong><?= Yii::t('amosapp', "cookie tecnici") ?></strong><?= Yii::t('amosapp', ', installati dal sito stesso al fine di monitorare il funzionamento del sito e consentire una navigazione efficiente sullo stesso.') ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', "Tali cookie sono strettamente necessari per il corretto funzionamento del sito o per consentire la fruizione dei contenuti e dei servizi richiesti dall’utente.") ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', "A norma dell’art. 122 del Codice per la protezione dei dati personali e del provvedimento del Garante per la protezione dei dati personali relativo all’“Individuazione delle modalità semplificate per l’informativa e l’acquisizione del consenso per l’uso dei cookies” dell’8 maggio 2014, pubblicato sulla Gazzetta Ufficiale n. 126 del 3 giugno 2014, è possibile installare nel browser degli utenti i cookies tecnici essenziali per il corretto funzionamento di un sito web, nonché quelli analytics di terze parti, assimilati ai cookie tecnici laddove utilizzati direttamente dal gestore del sito per raccogliere informazioni, in forma aggregata, sul numero degli utenti e su come questi visitano il sito stesso, fermo restando l’obbligo di informativa ai sensi dell’art. 13 del D. lgs. 196/2013.") ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', "Il presente sito utilizza, fra i cookie di terze parti assimilabili a quelli tecnici, i cookie analytics di Google al fine di analizzare statisticamente gli accessi o le visite al sito stesso e consentire al titolare di migliorarne la struttura, le logiche di navigazione e i contenuti. Tali cookie, opportunamente anonimizzati (es. mascheramento indirizzo IP), consentono la raccolta di informazioni aggregate sul numero degli utenti e su come questi visitano il sito senza poter identificare il singolo utente.") ?>
    </p>
    <br>
    <p>
        <ins><?= Yii::t('amosapp', "Cookie di Social Network") ?></ins>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', "Il presente portale consente agli utenti di condividere i relativi contenuti sui Social Network (come Youtube, Twitter) accedendo direttamente ai siti dei suddetti Social Network. Eventuali cookie potrebbero pertanto essere istallati su tali siti (e non sul presente portale). Regione Lombardia non è a conoscenza della finalità dei cookie eventualmente istallati dai suddetti Social Network sui relativi siti. Al fine di permettere agli utenti di conoscere modalità e finalità dei trattamenti delle informazioni degli utenti da parte delle Terze parti, si riportano i seguenti link alle informative privacy e le cookie policy dalle Terze parti coinvolte:") ?>
    </p>
    <br>
    <p>
        <strong><?= Yii::t('amosapp', "Google") ?></strong>
    </p>
    <p>
        <?= Html::a('https://www.google.it/intl/it/policies/privacy/', 'https://www.google.it/intl/it/policies/privacy/') ?>
    </p>
    <br>
    <p>
        <strong><?= Yii::t('amosapp', "Twitter") ?></strong>
    </p>
    <p>
        <?= Html::a('https://twitter.com/privacy?lang=it', 'https://twitter.com/privacy?lang=it') ?>
    </p>
    <p>
        <?= Html::a('https://support.twitter.com/articles/20170519#', 'https://support.twitter.com/articles/20170519#') ?>
    </p>
    <br>
    <p>
        <strong>
            <ins><?= Yii::t('amosapp', "Non sono utilizzati sul presente sito cookie di profilazione né di prima né di terza parte.") ?></ins>
        </strong>
    </p>
    <br>
    <h3 class="bold"><?= Yii::t('amosapp', "È possibile disabilitare i cookie?") ?></h3>
    <p>
        <?= Yii::t('amosapp', "Si segnala che di default quasi tutti i browser web sono impostati per accettare automaticamente i cookie. I navigatori possono comunque modificare la configurazione predefinita tramite le impostazioni del browser utilizzato, che consentono di cancellare/rimuovere tutti o alcuni cookie o bloccare l’invio dei cookie o limitarlo a determinati siti. La disabilitazione/il blocco dei cookies o la loro cancellazione potrebbe però compromettere la fruizione ottimale di alcune aree del sito o impedire alcune funzionalità, nonché influire sul funzionamento dei servizi delle terze parti.") ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', "La configurazione della gestione dei cookie dipende dal browser utilizzato. Si riportano, di seguito i link alle guide per le gestione dei cookie dei principali browser:") ?>
    </p>
    <br>
    <p>
        <strong><?= Yii::t('amosapp', "Microsoft Internet Explorer") ?></strong>
    </p>
    <p>
        <?= Yii::t('amosapp', 'Da "Strumenti" selezionare "Opzioni internet". Nella finestra pop up selezionare "Privacy" e regolare le impostazioni dei cookies oppure tramite i link:') ?>
    </p>
    <br>
    <p>
        <?= Html::a('http://windows.microsoft.com/en-us/windows-vista/block-or-allow-cookies', 'http://windows.microsoft.com/en-us/windows-vista/block-or-allow-cookies') ?>
    </p>
    <p>
        <?= Html::a('http://windows.microsoft.com/it-it/internet-explorer/delete-manage-cookies#ie=ie-9', 'http://windows.microsoft.com/it-it/internet-explorer/delete-manage-cookies#ie=ie-9') ?>
    </p>
    <br>
    <p>
        <strong><?= Yii::t('amosapp', "Google Chrome per Desktop") ?></strong>
    </p>
    <p>
        <?= Yii::t('amosapp', 'Selezionare "Impostazioni", poi "Mostra impostazioni avanzate", successivamente nella sezione "Privacy" selezionare "Impostazione Contenuti" e regolare le impostazioni dei cookie oppure accedere tramite i link:') ?>
    </p>
    <br>
    <p>
        <?= Html::a('https://support.google.com/chrome/answer/95647?hl=it&p=cpn_cookies', 'https://support.google.com/chrome/answer/95647?hl=it&p=cpn_cookies') ?>
    </p>
    <p>
        <?= Html::a('https://support.google.com/accounts/answer/61416?hl=it', 'https://support.google.com/accounts/answer/61416?hl=it') ?>
    </p>
    <br>
    <p>
        <strong><?= Yii::t('amosapp', "Google Chrome per Mobile") ?></strong>
    </p>
    <p>
        <?= Yii::t('amosapp', 'Accedere tramite link') ?>
    </p>
    <br>
    <p>
        <?= Html::a('https://support.google.com/chrome/answer/2392971?hl=it', 'https://support.google.com/chrome/answer/2392971?hl=it') ?>
    </p>
    <br>
    <p>
        <strong><?= Yii::t('amosapp', "Mozilla Firefox") ?></strong>
    </p>
    <p>
        <?= Yii::t('amosapp', 'Selezionare "Opzioni" e nella finestra di pop up selezionare "Privacy" per regolare le impostazioni dei cookie, oppure accedere tramite i link') ?>
    </p>
    <br>
    <p>
        <?= Html::a('https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie?redirectlocale=en-US&redirectslug=Enabling+and+disabling+cookies', 'https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie?redirectlocale=en-US&redirectslug=Enabling+and+disabling+cookies') ?>
    </p>
    <p>
        <?= Html::a('https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie', 'https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie') ?>
    </p>
    <br>
    <p>
        <strong><?= Yii::t('amosapp', "Apple Safari") ?></strong>
    </p>
    <p>
        <?= Yii::t('amosapp', 'Selezionare "Preferenze" e poi "Sicurezza" dove regolare le impostazioni dei cookie oppure accedere tramite il link:') ?>
    </p>
    <br>
    <p>
        <?= Html::a('https://support.apple.com/it-it/HT201265', 'https://support.apple.com/it-it/HT201265') ?>
    </p>
    <br>
    <p>
        <strong><?= Yii::t('amosapp', "Opera") ?></strong>
    </p>
    <p>
        <?= Yii::t('amosapp', 'Selezionare "Preferenze", poi "Avanzate" e poi "Cookie" dove regolare le impostazioni dei cookie oppure accedere tramite il link:') ?>
    </p>
    <br>
    <p>
        <?= Html::a('http://www.opera.com/help/tutorials/security/cookies/', 'http://www.opera.com/help/tutorials/security/cookies/') ?>
    </p>
    <p>
        <?= Html::a('http://help.opera.com/Windows/10.00/it/cookies.html', 'http://help.opera.com/Windows/10.00/it/cookies.html') ?>
    </p>
    <br>
    <p>
        <strong><?= Yii::t('amosapp', "Browser nativo Android") ?></strong>
    </p>
    <p>
        <?= Yii::t('amosapp', 'Selezionare "Impostazioni", poi "Privacy" e selezionare o deselezionare la casella "Accetta cookie".') ?>
    </p>
    <br>
    <p>
        <?= Yii::t('amosapp', 'Per i browser diversi da quelli sopra elencati consultare la relativa guida per individuare le modalità di gestione dei cookie.') ?>
    </p>
</section>
