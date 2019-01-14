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
    'errorHandler' => [
        'errorAction' => 'site/error',
    ],
    'eventSequence' => [
        'class' => '\raoul2000\workflow\events\BasicEventSequence',
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ],
        ],
    ],
    'request' => [
        'csrfParam' => '_csrf-backend',
    ],
    'session' => [
        // this is the name of the session cookie used for login on the backend
        'name' => 'advanced-backend',
        'timeout' => (24 * 60 * 60), // 1 day
    ],
    'user' => [
        'class' => 'lispa\amos\core\user\AmosUser',
        'identityClass' => 'lispa\amos\core\user\User',
        'loginUrl' => '/admin/security/login',
        'enableAutoLogin' => true,
        'authTimeout' => (24 * 60 * 60), // 1 day
        'identityCookie' => [
            'name' => '_identity-backend',
            'httpOnly' => true,
            'secure' => true
        ],
    ],
];