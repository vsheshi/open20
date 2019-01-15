<?php

use yii\helpers\Html;
use lispa\amos\utility\Module;

/* @var $this \yii\web\View */

$this->title = Module::t('amosutility', 'File Manager');

$src = Yii::getAlias('@vendor/lispa/amos-utility/src');

define('FM_EMBED', true);
define('FM_ROOT_PATH', Yii::getAlias('@app').'/..');
define('FM_SELF_URL', '/' . Yii::$app->controller->getRoute());

require $src . '/libs/filemanager.php';