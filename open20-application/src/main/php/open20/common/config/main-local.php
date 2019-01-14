<?php

/**
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=test_pcd_delete', //demos_pcd20
            'username' => 'demos',
            'password' => 'demospwd',
            'charset' => 'utf8',
            'enableSchemaCache' => false,
            'schemaCacheDuration' => 88000,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.demotestwip.it',
                'username' => 'ambiente.demo@demotestwip.it',
                'password' => '1D4jmVgs93K48w5f',
                'port' => '25',
                //'encryption' => 'tls',
            ],
        ],
    ],
];
