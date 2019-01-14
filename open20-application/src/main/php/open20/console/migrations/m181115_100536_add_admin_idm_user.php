<?php
use yii\db\Migration;

/**
 * Class m181115_100536_add_admin_idm_user
 */
class m181115_100536_add_admin_idm_user extends Migration
{
    /**
     * Tble
     */
    const TABLE_NAME = 'social_idm_user';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->insert(self::TABLE_NAME, [
            'user_id' => '1',
            'numeroMatricola' => 'MRGRNT57R18F205V',
            'codiceFiscale' => 'MRGRNT57R18F205V',
            'nome' => null,
            'cognome' => null,
            'ssoDN' => null,
            'tipoAutenticazione' => null,
            'ssoUrlLogout' => null,
            'responseBase64' => null,
            'emailAddress' => null,
            'livelloVerifica' => null,
            'nomeUtente' => null,
            'sesso' => null,
            'dataNascita' => null,
            'luogoNascita' => null,
            'provinciaNascita' => null,
            'statoNascita' => null,
            'identificativoUtente' => null,
            'cellulare' => null,
            'ragioneSociale' => null,
            'sedeLegale' => null,
            'partitaIVA' => null,
            'docIdentita' => null,
            'scadDocIdentita' => null,
            'domicilioFisico' => null,
            'domicilioDigitale' => null,
            'origineDatiUtente' => null,
            'statoValidazioneProfiloUtente' => null,
            'idComuneRegistrazione' => null
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function safeDown()
    {

        $this->delete(self::TABLE_NAME, [
            'user_id' => '1',
            'numeroMatricola' => 'MRGRNT57R18F205V',
            'codiceFiscale' => 'MRGRNT57R18F205V',
            'nome' => null,
            'cognome' => null,
            'ssoDN' => null,
            'tipoAutenticazione' => null,
            'ssoUrlLogout' => null,
            'responseBase64' => null,
            'emailAddress' => null,
            'livelloVerifica' => null,
            'nomeUtente' => null,
            'sesso' => null,
            'dataNascita' => null,
            'luogoNascita' => null,
            'provinciaNascita' => null,
            'statoNascita' => null,
            'identificativoUtente' => null,
            'cellulare' => null,
            'ragioneSociale' => null,
            'sedeLegale' => null,
            'partitaIVA' => null,
            'docIdentita' => null,
            'scadDocIdentita' => null,
            'domicilioFisico' => null,
            'domicilioDigitale' => null,
            'origineDatiUtente' => null,
            'statoValidazioneProfiloUtente' => null,
            'idComuneRegistrazione' => null
        ]);
    }
}
