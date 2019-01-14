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
    'assetManager' => [
        'appendTimestamp' => true,
        'forceCopy' => false,
        'converter' => [
            'class' => 'cakebake\lessphp\AssetConverter',
            'compress' => true,
            'useCache' => false,
            //'cacheDir' => null,
            'cacheSuffix' => true,
        ],
    ],
    'authManager' => [
        'class' => 'lispa\amos\core\rbac\DbManagerCached', // class for cache rbac role and speed up query 'lispa\amos\core\rbac\DbManagerCached',
    ],
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'breadcrumbcache' => [
        'class' => 'yii\caching\FileCache',
        'cachePath' => '@runtime/breadcrumbcache'
    ],
    'eventSequence' => [
        'class' => '\raoul2000\workflow\events\BasicEventSequence',
    ],
    'i18n' => [
        'translations' => [
            '*' => [
                'class' => 'lispa\amos\core\i18n\MessageSource',
                'db' => 'db',
                'sourceLanguage' => 'it-IT', // Developer language
                'sourceMessageTable' => '{{%language_source}}',
                'messageTable' => '{{%language_translate}}',
                'cachingDuration' => 86400,
                'enableCaching' => true,
                'forceTranslation' => true,
                'autoUpdate' => true,
                'extraCategoryPaths' => [
                    'amoscore' => '@vendor/lispa/amos-core/i18n',
                    //DISATTIVATE DI PROPOSITO CAUSA LABEL MODIFICATE IN COMMUNITY (?)
                    //'amoscommunity' => '@vendor/lispa/amos-community/src/i18n',
                    'amos' => '@common/translation/amos/i18n',
                    'amosplatform' => '@common/translation/amosplatform/i18n',
                    'amosapp' => '@common/translation/amosapp/i18n',
                    'cruds' => '@common/translation/cruds/i18n',
                    'giiamos' => '@common/translation/giiamos/i18n',
                    'javascript' => '@common/translation/javascript/i18n',
                    'site' => '@common/translation/site/i18n',
                    'wizard' => '@common/translation/wizard/i18n',
                ],
            ],
        ],
    ],
    'translatemanager' => [
        'class' => 'lajax\translatemanager\Component'
    ],
    'urlManager' => [
        'class' => 'yii\web\UrlManager',
        // Disable index.php
        'showScriptName' => false,
        // Disable r= routes
        'enablePrettyUrl' => true,
        'rules' => array(
            '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>] => <module>/<controller>/<action>',
        ),
    ],
];