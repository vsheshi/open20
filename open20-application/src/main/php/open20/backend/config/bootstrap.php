<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

$bootstrap[] = 'translatemanager';

$bootstrap[] = [
    'class' => 'lispa\amos\core\components\LanguageSelector',
    'supportedLanguages' => ['it-IT'],
    'allowedIPs' => ['*']
];

$bootstrap[] = 'lispa\amos\core\bootstrap\Breadcrumb';
$bootstrap[] = 'lispa\amos\admin\bootstrap\CheckPasswordExpirationComponent';
$bootstrap[] = 'workflow';
//$bootstrap[] = 'notify';
$bootstrap[] = 'comments';
$bootstrap[] = 'layout';
$bootstrap[] = 'socialauth';


return $bootstrap;
