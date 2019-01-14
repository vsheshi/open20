<?php

/**
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

$googleMapsApiKey = 'AIzaSyDiW9sF2p9EEQP0s0Rw34_IA2czj2bjSoI';

$params = [
    'google-maps' => [
        'key' => $googleMapsApiKey
    ],
    'googleMapsApiKey' => $googleMapsApiKey,
    'googleMapsLanguage' => 'it',
    'versione' => '1.2.15', // Version
    'hideWorkflowTransitionWidget' => true,
    'logo' => '/img/logo_PCD.jpg',
];

return $params;
