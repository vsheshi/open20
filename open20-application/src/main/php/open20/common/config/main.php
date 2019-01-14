<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$modules = array_merge(
    require(__DIR__ . '/modules-others.php'),
    require(__DIR__ . '/modules-amos.php')
);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$bootstrap = array_merge(
    require(__DIR__ . '/bootstrap-extra.php')
);


if (isset($modules['chat'])) {
    $bootstrap[] = 'chat';
}
if (isset($modules['cwh'])) {
    $bootstrap[] = 'cwh';
}

if (isset($modules['layout'])) {
  $bootstrap[] = 'layout';
}

if (isset($modules['tag'])) {

    if (isset($modules['community'])) {
        $modules['tag']['modelsEnabled'][] = 'lispa\amos\community\models\Community';
    }
    if (isset($modules['discussioni'])) {
        $modules['tag']['modelsEnabled'][] = 'lispa\amos\discussioni\models\DiscussioniTopic';
    }
    if (isset($modules['documenti'])) {
        $modules['tag']['modelsEnabled'][] = 'lispa\amos\documenti\models\Documenti';
    }
    if (isset($modules['events'])) {
        $modules['tag']['modelsEnabled'][] = 'lispa\amos\events\models\Event';
    }
    if (isset($modules['news'])) {
        $modules['tag']['modelsEnabled'][] = 'lispa\amos\news\models\News';
    }
    $bootstrap[] = 'tag';
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$components = array_merge(
    require(__DIR__ . '/components-others.php'),
    require(__DIR__ . '/components-amos.php')
);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
return [
    'aliases' => [
        '@file' => dirname(__DIR__),
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'it-IT',
    'timeZone' => 'Europe/Rome',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    'bootstrap' => $bootstrap,
    'components' => $components,
    'modules' => $modules,
    'params' => $params,
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
];
