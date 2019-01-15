<?php

use yii\helpers\Html;
use lispa\amos\utility\Module;

/* @var $this \yii\web\View */

$this->title = Module::t('amosutility', 'Functions List');
?>
<div class="default-index">
    <div class="row">
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'Database'); ?></h3>
            <p><?= Module::t('amosutility', 'Manage Database with Adminer'); ?></p>
            <p><?= Html::a('Start &raquo;', ['database/index'], ['class' => 'btn btn-default']) ?></p>
        </div>
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'File Manager'); ?></h3>
            <p><?= Module::t('amosutility', 'Browser and manage files'); ?></p>
            <p><?= Html::a('Start &raquo;', ['files/index'], ['class' => 'btn btn-default']) ?></p>
        </div>
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'Packages'); ?></h3>
            <p><?= Module::t('amosutility', 'See all installed packages (composer)'); ?></p>
            <p><?= Html::a('Start &raquo;', ['packages/index'], ['class' => 'btn btn-default']) ?></p>
        </div>
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'Logs'); ?></h3>
            <p><?= Module::t('amosutility', 'Browse Logs'); ?></p>
            <p><?= Html::a('Start &raquo;', ['logs/index'], ['class' => 'btn btn-default']) ?></p>
        </div>
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'Requirements'); ?></h3>
            <p><?= Module::t('amosutility', 'Check if app requirements are satisfied'); ?></p>
            <p><?= Html::a('Start &raquo;', ['packages/requirements'], ['class' => 'btn btn-default']) ?></p>
        </div>
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'Tasks'); ?></h3>
            <p><?= Module::t('amosutility', 'Manage Applications TAsks'); ?></p>
            <p><?= Html::a('Start &raquo;', ['tasks/index'], ['class' => 'btn btn-default']) ?></p>
        </div>
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'Workflow Manager'); ?></h3>
            <p><?= Module::t('amosutility', 'Manage workflow'); ?></p>
            <p><?= Html::a('Start &raquo;', ['/workflow-manager'], ['class' => 'btn btn-default']) ?></p>
        </div>
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'Translate Manager'); ?></h3>
            <p><?= Module::t('amosutility', 'Manage Translation'); ?></p>
            <p><?= Html::a('Start &raquo;', ['/translatemanager'], ['class' => 'btn btn-default']) ?></p>
        </div>
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'Reset dashboard'); ?></h3>
            <p><?= Module::t('amosutility', 'Reset dashboard'); ?></p>
            <p><?= Html::a('Start &raquo;', ['default/reset-dashboard-by-module'], ['class' => 'btn btn-default']) ?></p>
        </div>
        <div class="generator col-lg-4">
            <h3><?= Module::t('amosutility', 'Run Console Command'); ?></h3>
            <p><?= Module::t('amosutility', 'Run Console Command'); ?></p>
            <p><?= Html::a('Start &raquo;', ['console/index'], ['class' => 'btn btn-default']) ?></p>
        </div>
    </div>

</div>
