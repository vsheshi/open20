<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

$bootstrap = [];

$bootstrap[] = 'translatemanager';

$bootstrap[] = [
    'class' => 'lispa\amos\core\components\LanguageSelector',
    'supportedLanguages' => ['en-GB', 'it-IT'],
    'allowedIPs' => ['*']
];

return $bootstrap;
