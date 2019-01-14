<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

return [
    'formatter' => [
        'class' => 'lispa\amos\core\formatter\Formatter',
        'dateFormat' => 'php:d/m/Y',
        'datetimeFormat' => 'php:d/m/Y H:i',
        'timeFormat' => 'php:H:i',
        'defaultTimeZone' => 'Europe/Rome',
        'timeZone' => 'Europe/Rome',
        'locale' => 'it-IT',
        'thousandSeparator' => '.',
        'decimalSeparator' => ',',
    ],
    'imageUtility' => [
        'class' => 'lispa\amos\core\components\ImageUtility',
    ],
    'view' => [
         'class' => 'lispa\amos\core\components\AmosView',
    ],
    'workflowSource' => [
        'class' => 'lispa\amos\core\workflow\ContentDefaultWorkflowDbSource',
    ],
];
