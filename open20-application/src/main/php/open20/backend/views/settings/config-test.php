<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use yii\bootstrap\Button;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\admin\models\Settings;

/**
 * @var yii\web\View $this
 */

$this->title = $model;
$this->params['breadcrumbs'][] = ['label' => AmosAdmin::t('amosplatform', 'Admin'), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => AmosAdmin::t('amosplatform', 'Platform Configurator'), 'url' => ['/admin/settings']];
//$this->params['breadcrumbs'][] = ['label' => $model, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = AmosAdmin::t('amosplatform', 'Configuration Test');
?>


<hr>

<h3><?= AmosAdmin::t('amosplatform', $result ? 'Success' : 'Failed'); ?></h3>

<hr>