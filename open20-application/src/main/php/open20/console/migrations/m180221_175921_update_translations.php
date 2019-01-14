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
class m180221_175921_update_translations extends AmosMigrationTranslations
{

    /**
     * @inheritdoc
     */
    protected function setTranslations()
    {
        return [
            self::LANG_IT => [
                ['category' => 'amosnews', 'source' => 'Notizie', 'update' => true, 'oldTranslation' => 'Notizie', 'newTranslation' => "Comunicazioni"],
                ['category' => 'amosnews', 'source' => 'Ultime notizie', 'update' => true, 'oldTranslation' => 'Ultime notizie', 'newTranslation' => "Ultime comunicazioni"],
                ['category' => 'amoscommunity', 'source' => '#widget_subcommunities_title', 'update' => true, 'oldTranslation' => 'Stanza', 'newTranslation' => "Stanze",],

            ]
        ];
    }
}