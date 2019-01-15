<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comuni
 * @category   CategoryName
 */

namespace lispa\amos\comuni\controllers;

use lispa\amos\comuni\models\IstatRegioni;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\gii\CodeFile;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use lispa\amos\comuni\models\IstatComuni;
use lispa\amos\comuni\models\IstatProvince;
use lispa\amos\comuni\AmosComuni;
use lispa\amos\comuni\controllers\util\XlsUtil;

class ImportatoreController extends Controller
{

    protected $file_directory =  null;
    /**
     * @var string $layout Layout per la dashboard interna.
     */
    //public $layout = '@vendor\lispa\amos-core\views\layouts\dashboard_interna';

    public function init()
    {
        parent::init();
        $this->file_directory = \Yii::getAlias("@vendor/lispa/amos-comuni/src/file_import/");
    }

    public function actionIndex(){
        $params = [];
        return $this->render('index', $params);
    }

    /**
     * @param $filename Nome del file all'interno del path /amos-comuni/src/file_import/
     * @param $type String [default false] se specificato prova a decodificare il contenuto del file
     * cerca il file e ne ritorna il contenuto
     */
    protected function loadFileData($filename, $type=false){
        $file = \Yii::getAlias($this->file_directory.$filename);

        if( !file_exists($file)){
            pr("ERRORE FILE MANCANTE IN {$file}");
        }

        switch ( strtolower($type) ){
            case 'json':
                return json_decode( file_get_contents($file));
                break;
            case 'xls':
            case 'xlsx':
                if (!XlsUtil::load($file)) {
                    throw new Exception("impossibile caricare il file: {$file}", 1);
                }
                //leggo il primo foglio
                $content_file = XlsUtil::toArray(0);
                return $content_file;
        }

        return file_get_contents($file);
    }

    /**
     * usa il file comuni.json per confrontare il codice castastale salvato a DB con quello del file:
     * se differenti viene impostato per essere aggiornato con quello del file.

     * Viene creata una migration contenente tutti i comuni da aggiornare (se presenti)
     */
    public function actionImportCodiciCatastali(){
        //pr(" ---- PROCEDURA DI IMPOSTAZIONE CodiciCatastali ----");
        $file_input = "comuni.json";

        $this->layout = "@vendor/lispa/amos-core/views/layouts/form";

        $arr_new = [];
        $arr_old = [];
        //recupero il contenuto del json comuni
        $file_content = $this->loadFileData($file_input, 'json');

        foreach ($file_content as $k => $comune){
        //if($k > 10){
            //continue;
        //}
        //pr($comune,"el.to"  );
        //pr($comune->nome,"--- COMUNE"  );

            $cod_catastale_input = $comune->codiceCatastale;

            if( isset($comune->codice) ){
                $comuneRecord = $this->findComuneRecordByCodice($comune->codice);

                if( is_object($comuneRecord) && $comuneRecord->id){
                //pr( $comuneRecord->toArray(), "comune trovato" );
                //pr($cod_catastale_input, "cod catastale input");
                //pr($comuneRecord->codice_catastale, "cod catastale comune");
                    //se ho un codice catastale in input, ed è diverso da quello salvato a DB: aggiorno
                    if( !empty($cod_catastale_input) && ($comuneRecord->codice_catastale != $cod_catastale_input)){
                        //pr("importa {$comune->nome}");
                        //memorizzo i dati che andrei ad aggiornare nella migration
                        $arr_new[] = [
                            'columns' =>   ["codice_catastale" => $cod_catastale_input],
                            'conditions' => ["id" => $comuneRecord->id]
                        ];

                        //memorizzo i dati per eventuale ripristino con migrate down
                        $arr_old[] = [
                            'columns' =>   ["codice_catastale" => $comuneRecord->codice_catastale],
                            'conditions' => ["id" => $comuneRecord->id]
                        ];

                        $dati[] = ['comuneArray' => $comuneRecord->toArray(),'new_codice_catastale' => $cod_catastale_input];
                    }
                }
            }else{
                Yii::$app->getSession()->addFlash('danger', Yii::t('app', 'Codice COMUNE mancante'));
                //pr("#### ERRORE CODICE COMUNE MANCANTE! ");
            }
        }//end foreach

        if( !empty($arr_new) && !empty(\Yii::$app->request->post('confirm') ) ){
            list($result_generate, $message_generate) = $this->generateMigrationFile('istat_comuni_update_cod_castastale', 'update','istat_comuni', $arr_new, $arr_old );

            if($result_generate === true){
                Yii::$app->getSession()->addFlash('success',  $message_generate);
            }else{
                Yii::$app->getSession()->addFlash('danger', $message_generate);
            }
        }

        return $this->render('import_codici_catastali', [
            'dati' => $dati,
            'url' => Url::current(),
            'generate_result' => $result_generate,
            'generate_message' => $message_generate,

        ]);

    }

    /**
     *
     * usa il file comuni.json per confrontare i CAP salvati a DB con quelli del file:
     * se ve ne sono di differenti, vengono impostati per essere inseriti a DB.

     * Viene creata una migration contenente tutti i cap da inserire (se presenti)
     */
    public function actionImportCap(){
        ini_set("memory_limit","2G");
        //pr(" ---- PROCEDURA DI IMPOSTAZIONE CAP ----");
        $file_input = "comuni.json";

        $this->layout = "@vendor/lispa/amos-core/views/layouts/form";

        $arr_up = [];
        $arr_down = [];
        //recupero il contenuto del json comuni
        $file_content = $this->loadFileData($file_input, 'json');

        foreach ($file_content as $k => $comune){
            /*if($k > 2){
                continue;
            }*/

            /*pr($comune,"el.to"  );
            pr($comune->nome,"--- COMUNE"  );*/

            $caps_comune_json = $comune->cap;

            if( isset($comune->codice) ){
                $comuneRecord = $this->findComuneRecordByCodice($comune->codice);

                if( is_object($comuneRecord) && $comuneRecord->id){
                    /*pr( $comuneRecord->toArray(), "comune trovato" );*/

                    //recupero i cap associati a DB
                    $caps_comune_db = $comuneRecord->getComuneCaps();
                    /*pr($caps_comune_db, "comuni DB");
                    pr($caps_comune_json, "comuni JSON");*/
                    //recupero i cap da inserire facendo la differenza tra quelli a DB e quelli in input da file JSON
                    $caps_to_insert = array_merge(array_diff($caps_comune_db,$caps_comune_json),array_diff($caps_comune_json,$caps_comune_db));
                    /*pr($caps_to_insert, "inter");*/

                    foreach ($caps_to_insert as $k_1 => $cap_to_insert ){
                        //memorizzo gli inserimenti per migrate UP
                        $arr_up[] = [
                            'columns' =>   ['comune_id', 'cap', 'sospeso'],
                            'values' => [$comuneRecord->id, $cap_to_insert, 0],
                        ];

                        //memorizzo le cancellazioni per migrate DOWN
                        $arr_down[] = [
                            'conditions' => ['comune_id' => $comuneRecord->id, 'cap' => $cap_to_insert]
                        ];

                    }
                    //per visualizzazione in anteprima
                    if(!empty($caps_to_insert)){
                        $dati[] = ['comuneArray' => $comuneRecord->toArray(),'new_caps' => $caps_to_insert];
                    }
                }
            }else{
                Yii::$app->getSession()->addFlash('danger', Yii::t('app', 'Codice COMUNE mancante'));
                //pr("#### ERRORE CODICE COMUNE MANCANTE! ");
            }
        }//end foreach

        if( !empty($arr_up) && !empty(\Yii::$app->request->post('confirm') ) ){
            list($result_generate, $message_generate) = $this->generateMigrationFile('istat_comuni_cap_insert', 'insert','istat_comuni_cap', $arr_up, $arr_down );

            if($result_generate === true){
                Yii::$app->getSession()->addFlash('success',  $message_generate);
            }else{
                Yii::$app->getSession()->addFlash('danger', $message_generate);
            }
        }

        return $this->render('import_cap', [
            'dati' => $dati,
            'url' => Url::current(),
            'generate_result' => $result_generate,
            'generate_message' => $message_generate,

        ]);
    }

    /**
     * @return string
     * usa il file Elenco-comuni-italiani.xls per importare i comuni che non sono presenti a DB:
     *
     * Se a DB il comune ricercato per ID non viene trovato => migration per inserimento
     * Se esiste MA il nome del comune NON corrisponde a quello in input => migration per aggiornamento nome
     *
     * Viene creata:
     *  - una migration contentente tutti i comuni da inserire
     *  - una migration contentente tutti i nomi dei comuni da aggiornare
     *
     */
    public function actionImportComuni(){
        ini_set("memory_limit","2G");
        //pr(" ---- PROCEDURA DI IMPOSTAZIONE Comuni ----");
        $file_input = "Elenco-comuni-italiani.xls";

        $this->layout = "@vendor/lispa/amos-core/views/layouts/form";

        $arr_up = [];
        $arr_down = [];
        $dati = [];
        $errors = [];
        //recupero il contenuto del xls comuni
        $file_content = $this->loadFileData($file_input, 'xls');
        //salto le intestazioni
        $file_content = array_slice($file_content,1);

        /** INTESTAZIONI
        [0] => Codice Regione
        [1] => Codice Città Metropolitana
        [2] => Codice Provincia (1)
        [3] => Progressivo del Comune (2)
        [4] => Codice Comune formato alfanumerico
        [5] => Denominazione in italiano
        [6] => Denominazione in tedesco
        [7] => Codice Ripartizione Geografica
        [8] => Ripartizione geografica
        [9] => Denominazione regione
        [10] => Denominazione Città metropolitana
        [11] => Denominazione provincia
        [12] => Flag Comune capoluogo di provincia
        [13] => Sigla automobilistica
        [14] => Codice Comune formato numerico
        [15] => Codice Comune numerico con 110 province (dal 2010 al 2016)
        [16] => Codice Comune numerico con 107 province (dal 2006 al 2009)
        [17] => Codice Comune numerico con 103 province (dal 1995 al 2005)
        [18] => Codice Catastale del comune
        [19] => Popolazione legale 2011 (09/10/2011)
        [20] => Codice NUTS1 2010
        [21] => Codice NUTS2 2010 (3)
        [22] => Codice NUTS3 2010
        [23] => Codice NUTS1 2006
        [24] => Codice NUTS2 2006 (3)
        [25] => Codice NUTS3 2006
         */

        foreach ($file_content as $k => $row_comune) {
            /*if($k > 50){
                continue;
            }*/
            //salto righe con dati vuoti
            if( empty($row_comune[0]) ){
                continue;
            }
            //pr($row_comune, "row comune");
            $cod_istat = $row_comune[4];
            $nome_comune = $row_comune[5];

            //cerco in istatComuni per cod_istat_alfanumerico e nome
            //$q_c = IstatComuni::find()->andWhere(['cod_istat_alfanumerico' => $cod_istat/*, 'nome' => $nome_comune*/]);
            $q_c = IstatComuni::find()->andWhere(['id' => intval($cod_istat)]);
            $exists = $q_c->count();
            //pr($exists, "esiste {$cod_istat} {$nome_comune}");

            //se il record esiste già controllo che il nome corrisponda: altrimenti va aggiornato
            if($exists){
                $comuneRecord = $q_c->one();
                //se il nome a DB è differente da quello in input
                if( $comuneRecord->nome != $nome_comune ){
                    //pr($nome_comune, "{$comuneRecord->nome} CAMBIA NOME in");
                    $arr_update[]= [
                            'columns' =>   ["nome" => addslashes($nome_comune)],
                            'conditions' => ["id" => $comuneRecord->id]
                    ];

                    $arr_restore[] = [
                            'columns' =>   ["nome" => addslashes($comuneRecord->nome)],
                            'conditions' => ["id" => $comuneRecord->id]
                    ];

                    //dati per anteprima a video
                    $dati['update'][] = ['comuneArray' => $comuneRecord->toArray(), 'nuovo_nome' => $nome_comune];
                }
            }

            //se il comune NON esiste a DB
            if(!$exists){
                //pr($row_comune, "row comune");
                //creo un array con tutti i valori da inserire per un record istatComuni
                $arr_values = $this->createArrayValuesByComune($row_comune);
                //pr($row_comune, "row");
                if(!$arr_values){
                    $nome_provincia = (($row_comune[11])!='-') ? $row_comune[11] : $row_comune[10];
                    $errors[] = "ERRORE: Comune NON trovato <strong>cod.ISTAT:</strong> {$row_comune[4]} <strong>Nome:</strong> {$row_comune[5]} <strong>Regione:</strong> {$row_comune[9]} <strong>Provincia:</strong> {$nome_provincia}";
                    continue;
                }
                //le colonne sono prese dalla struttura generata
                $arr_columns = array_keys($arr_values);
                //pr($arr_values, "arr values");
                $arr_up[] = [
                    'columns' =>  $arr_columns,
                    'values' => $arr_values,
                ];

                //memorizzo le cancellazioni per migrate DOWN
                $arr_down[] = [
                    'conditions' => ['id' => intval($row_comune[4])]
                ];

                //per visualizzazione in anteprima
                if(!empty($arr_values)){
                    $dati['new'][] = ['comuneArray' => $row_comune];
                }
            }
        }//end foreach comuni da file

        //se ho errori li visualizzo a video
        if( !empty($errors)){
            foreach ($errors as $k_e => $error){
                Yii::$app->getSession()->addFlash('danger', $error);
            }
        }

        /** genero il file MIGRATION per i NUOVI comuni */
        if( !empty($arr_up) && !empty(\Yii::$app->request->post('confirm') ) ){
            list($result_generate, $message_generate) = $this->generateMigrationFile('istat_comuni_insert', 'insert','istat_comuni', $arr_up, $arr_down );

            if($result_generate === true){
                Yii::$app->getSession()->addFlash('success',  $message_generate);
            }else{
                Yii::$app->getSession()->addFlash('danger', $message_generate);
            }

            /** genero il file MIGRATION per il cambio nome comuni */
            list($result_generate_update, $message_generate_update) = $this->generateMigrationFile('istat_comuni_update_nome', 'update','istat_comuni', $arr_update, $arr_restore );

            if($result_generate_update === true){
                Yii::$app->getSession()->addFlash('success',  $message_generate_update);
            }else{
                Yii::$app->getSession()->addFlash('danger', $message_generate_update);
            }
        }


        return $this->render('import_comuni', [
            'dati' => $dati,
            'url' => Url::current(),
            'errors' => $errors,
            /*'generate_result' => $result_generate,
            'generate_message' => $message_generate,*/

        ]);
    }

    /**
     * @param $comune_row_array Array rappresentante una riga del file 'Elenco-comuni-italiani.xls'
     * @return array|bool
     * Prepara un array con i nomi delle colonne della tabella istat_comuni data una riga del file
     * Ritorna un Array se tutto va bene, false se o Regione o Provinica NON sono state recuperate a DB
     *
     * NB: ricava regione e provincia ID da nome regione e nome provincia (col 9 e 11 (10) dell'array in input)
     */
    private function createArrayValuesByComune($comune_row_array){
        $RegioneRecord = IstatRegioni::findOne(['nome' => $comune_row_array[9]]);
        $ProvinciaRecord = IstatProvince::findOne(['nome' => $comune_row_array[11]]);
        //i campi indicanti la provincia sono "2", se non trovo per il primo, cerco per "denominazione città metropolitana"
        if(!is_object($ProvinciaRecord) || !$ProvinciaRecord->id){
            $ProvinciaRecord = IstatProvince::findOne(['nome' => $comune_row_array[10]]);
        }

        if(!is_object($RegioneRecord) || !$RegioneRecord->id || !is_object($ProvinciaRecord) || !$ProvinciaRecord->id){
            return false;
        }

        $ret_array = [
            'id' =>                             intval($comune_row_array[4]),
            'nome' =>                           addslashes($comune_row_array[5]),
            'progressivo' =>                    intval($comune_row_array[3]),
            'nome_tedesco' =>                   $comune_row_array[6],
            'cod_ripartizione_geografica' =>    $comune_row_array[7],
            'comune_capoluogo_provincia' =>     $comune_row_array[8],
            'cod_istat_alfanumerico' =>         $comune_row_array[4],
            'codice_2006_2009' =>               $comune_row_array[16],
            'codice_1995_2005' =>               $comune_row_array[17],
            'codice_catastale' =>               $comune_row_array[18],
            'popolazione_20111009' =>            $comune_row_array[19],
            'codice_nuts1_2010' =>              $comune_row_array[20],
            'codice_nuts2_2010' =>              $comune_row_array[21],
            'codice_nuts3_2010' =>              $comune_row_array[22],
            'codice_nuts1_2006' =>              $comune_row_array[23],
            'codice_nuts2_2006' =>              $comune_row_array[24],
            'codice_nuts3_2006' =>              $comune_row_array[25],
            'soppresso' =>                      0,
            'istat_unione_dei_comuni_id' =>     NULL,
            'istat_regioni_id' =>               $RegioneRecord->id,
            'istat_province_id' =>              $ProvinciaRecord->id,
        ];

        return $ret_array;
    }

    /**
     * @return string
     * usa il file Variazioni_amministrative_territoriali_dal_01011991.xls per settare sospesi i comuni che non lo sono già a DB:
     *
     * Legge tutti i dati nel file con colonna 'Tipo variazione' = 'ES' cioè estinzione
     * cerca il corrispondente record a DB e se questo NON è giò settato come soppresso => genera migration con i comuni da settare soppressi
     *
     * Viene creata:
     *  - migration con i comuni da settare soppressi
     */
    public function actionImportVariazioniComuni(){
        ini_set("memory_limit","2G");
        //pr(" ---- PROCEDURA DI IMPOSTAZIONE CAP ----");
        $file_input = "Variazioni_amministrative_territoriali_dal_01011991.xls";

        $this->layout = "@vendor/lispa/amos-core/views/layouts/form";

        $arr_update = [];
        $arr_restore = [];
        $dati = [];
        $errors = [];
        //recupero il contenuto del json comuni
        $file_content = $this->loadFileData($file_input, 'xls');
        //salto le intestazioni
        $file_content = array_slice($file_content,1);

        /**
         * INTESTAZIONI
            [0] => Anno
            [1] => Tipo variazione
            [2] => Codice Regione
            [3] => Codice Istat del Comune
            [4] => Denominazione Comune
            [5] => Codice Istat del Comune associato alla variazione o nuovo codice Istat del Comune
            [6] => Denominazione Comune associata alla variazione o nuova denominazione
            [7] => Provvedimento e Documento
            [8] => Contenuto del provvedimento
            [9] => Data decorrenza validità amministrativa
         */
        foreach ($file_content as $k => $variazione) {
            /*if($k > 500){
                continue;
            }*/

            $tipo_variazione = $variazione['1'];
            $cod_regione_comune = $variazione['2'];
            $cod_istat_comune = $variazione['3'];
            $nome_comune = $variazione['4'];
            $cod_istat_comune_nuovo = $variazione['5'];
            $nome_comune_nuovo = $variazione['6'];

            //considero SOLO le tipologia ES => estinzione
            if($tipo_variazione != 'ES'){
                continue;
            }

            //pr($variazione,"el.to"  );
            //pr($variazione['1'],"TIPO"  );
            //cerco il comune da impostare come soppresso
            $q_c = IstatComuni::find()->andWhere(['id' => intval($cod_istat_comune)]);
            $comuneRecord = $q_c->one();

            if( is_object($comuneRecord) && $comuneRecord->id ){
                //se il comune è già soppresso a DB: salto
                //NB: NON lo aggiungo come condizione alla query perchè voglio tenere traccia dei record trovati o meno
                if( $comuneRecord->soppresso ){
                    continue;
                }
                $arr_update[]= [
                    'columns' =>   ["soppresso" => 1],
                    'conditions' => ["id" => $comuneRecord->id]
                ];

                $arr_restore[] = [
                    'columns' =>   ["soppresso" => $comuneRecord->soppresso],
                    'conditions' => ["id" => $comuneRecord->id]
                ];

                $dati['update'][] = [ 'comuneRecord' => $comuneRecord ];
            }
            else{
                $errors[] = "ERRORE: Comune NON trovato <strong>cod.ISTAT:</strong> {$cod_istat_comune} <strong>Nome:</strong> {$nome_comune}";
            }
        }

        //se ho errori li visualizzo a video
        if( !empty($errors)){
            foreach ($errors as $k_e => $error){
                Yii::$app->getSession()->addFlash('danger', $error);
            }
        }

        /** genero il file MIGRATION per i NUOVI comuni */
        if( !empty($arr_update) && !empty(\Yii::$app->request->post('confirm') ) ){
            /** genero il file MIGRATION per il cambio nome comuni */
            list($result_generate_update, $message_generate_update) = $this->generateMigrationFile('istat_comuni_variazioni', 'update','istat_comuni', $arr_update, $arr_restore );

            if($result_generate_update === true){
                Yii::$app->getSession()->addFlash('success',  $message_generate_update);
            }else{
                Yii::$app->getSession()->addFlash('danger', $message_generate_update);
            }
        }

        return $this->render('import_variazioni_comuni', [
            'dati' => $dati,
            'url' => Url::current(),
            /*'generate_result' => $result_generate,
            'generate_message' => $message_generate,*/

        ]);
    }

    /**
     * @param $codice_istat String rappresenta un codice istat nel formato es: 028001
     * @return Record IstatComuni/ false se problemi
     */
    private function findComuneRecordByCodice($codice_istat){
        if( !empty($codice_istat)){
            $id_parsed = intval($codice_istat);
            if($id_parsed){
                $record = IstatComuni::findOne(['id' => $id_parsed]);

                if( is_object($record) && $record->id ){
                    return $record;
                }
                return false;
            }
        }
        return false;
    }


    /**
     * @param $nome_migration String nome migration postposta al formato m#DATA_FILE#
     * @param string $type_migration_skel String tipo migration da eseguire ( update/insert )
     * @param $table_name String nome tabella
     * @param $new_data Array dati per migrationUp
     * @param $restore_data Array dati per migrationDown
     * @return array con result/messaggio operazione
     * Crea il file migration in base ai parametri ricevuti in input
     * Ritoran un array con - valore Boolean per esisto operazione  - messaggio dell'operazione eseguita
     */
    protected function generateMigrationFile($nome_migration, $type_migration_skel='update', $table_name, $new_data, $restore_data ){
        //nome della migration da creare
        $migrationName = 'm' . date('ymd_His') . '_' . $nome_migration;

        //attivare in caso di debug per evitare che vengano create troppe migration
        /*$migrationName = 'm171231_060001_' . $nome_migration;*/

        $path_migration = '@vendor/lispa/amos-comuni/src/migrations';
        $complete_path = $path_migration.'/'.$migrationName.'.php';
        $path_destination = \Yii::getAlias($complete_path);

        $params = [
            'table_name' => $table_name,
            'new_data' => $new_data,
            'restore_data' => $restore_data,
            'migrationName' => $migrationName,
        ];

        switch ($type_migration_skel){
            case 'update':
                $path_migration_skel = \Yii::getAlias('@vendor/lispa/amos-comuni/src/views/importatore/importatore_migration_skel/MigrationUpdate.php');
                break;
            case 'insert':
                $path_migration_skel = \Yii::getAlias('@vendor/lispa/amos-comuni/src/views/importatore/importatore_migration_skel/MigrationInsert.php');
                break;
        }

        //pr($path_destination, "destination path generated file");
        $file = new CodeFile(
            $path_destination , $this->renderFile($path_migration_skel, $params)
        );

        if( !$file->save() ){
            return [false, "Migration NON salvata"];
        }

        //pr($path_destination, "FILE CORRETTAMENTE GENERATO IN:");
        return [true, "Migration salvata in: {$path_destination}"];
    }
}