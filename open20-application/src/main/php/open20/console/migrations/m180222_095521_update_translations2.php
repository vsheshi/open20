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
class m180222_095521_update_translations2 extends AmosMigrationTranslations
{

    /**
     * @inheritdoc
     */
    protected function setTranslations()
    {
        return [
            self::LANG_IT => [
                ['category' => 'amoscommunity', 'source' => 'Welcome to the community!', 'update' => true, 'oldTranslation' => 'Bevenuto nella community!', 'newTranslation' => "Benvenuto nella Area di lavoro!",],
            ]
        ];
    }
}