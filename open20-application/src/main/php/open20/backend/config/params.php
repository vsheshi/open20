<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

use lispa\amos\core\module\BaseAmosModule;

return [
    'adminEmail' => 'pcd@servizirl.it',
    'email-assistenza' => 'pcd@servizirl.it',
    'assistance' => [
        'email' => 'supportopcd@lispa.it',
    ],
    'icon-framework' => 'am',
    'googleMapsLibraries'=> null,
    'googleMapsLanguage' =>'en',
    'dont-use-logo' => TRUE,
    'hideSettings' => [
        'roles' => ['BASIC_USER']
    ],
    
    'favicon' => 'amos_favicon.png',
    
    // Enable Amos Exclusive features
    'template-amos' => FALSE,
    
    // Enable template slideshow
    'slideshow' => TRUE,
    'slideshow-label' => 'Mostra introduzione', // TODO translate and amos-XXX::[t()|tHtml()] ?

    // Enable Localization menu
    'languageSelector' => TRUE,
    'allLanguages' => ['Italiano' => 'it-IT'],
    'hideWorkflowTransitionWidget' => true,
    'privacyLink' => [
        'label' => BaseAmosModule::t('app', 'Informativa sulla privacy'),
        'url' => ['/site/privacy'],
        'linkOptions' => ['title' => BaseAmosModule::t('app', 'Informativa sulla privacy')]
    ],
    'cookiesLink' => [
        'label' => BaseAmosModule::t('app', 'Informativa sui cookies'),
        'url' => ['/site/cookies'],
        'linkOptions' => ['title' => BaseAmosModule::t('app', 'Informativa sui cookies')]
    ],
];
