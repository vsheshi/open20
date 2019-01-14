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
class m180117_175921_update_translations2 extends AmosMigrationTranslations
{

    /**
     * @inheritdoc
     */
    protected function setTranslations()
    {
        return [
            self::LANG_IT => [
                ['category' => 'amosadmin', 'source' => 'Community Managers', 'update' => true, 'oldTranslation' => 'Community Managers', 'newTranslation' => "Gestori area"]
            ]
        ];
    }
}