<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package
 * @category   CategoryName
 */
use lispa\amos\core\migration\AmosMigrationTranslations;

/**
 * Class m171216_181921_update_translations
 */
class m180301_120421_update_translations3 extends AmosMigrationTranslations
{

    /**
     * @inheritdoc
     */
    protected function setTranslations()
    {
        return [
            self::LANG_IT => [
                ['category' => 'amosdocumenti', 'source' => '#btn_new_folder', 'update' => true, 'oldTranslation' => 'Nuova cartella', 'newTranslation' => "Crea una nuova cartella"],
                ['category' => 'amosdocumenti', 'source' => '#updated_by', 'update' => true, 'oldTranslation' => 'Modificato da', 'newTranslation' => "Caricato da"],
                ['category' => 'amosnews', 'source' => 'Notizie', 'update' => true, 'oldTranslation' => 'Comunicazioni', 'newTranslation' => "Notizie"],
                ['category' => 'amosnews', 'source' => 'Ultime notizie', 'update' => true, 'oldTranslation' => 'Ultime comunicazioni', 'newTranslation' => "Ultime notizie"],
                ['category' => 'amoscommunity', 'source' => '#widget_subcommunities_title', 'update' => true, 'oldTranslation' => 'Stanza', 'newTranslation' => "Stanze"],
            ]
        ];
    }
}