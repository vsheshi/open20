<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

use lispa\amos\discussioni\AmosDiscussioni;

/**
 * @var yii\web\View $this
 * @var lispa\amos\discussioni\models\DiscussioniTopic $model
 */

/** @var \lispa\amos\discussioni\controllers\DiscussioniTopicController $controller */
$controller = Yii::$app->controller;
$controller->setNetworkDashboardBreadcrumb();
$this->title = AmosDiscussioni::t('amosdiscussioni', 'Nuova {modelClass}', [
    'modelClass' => 'Discussione',
]);
$this->params['breadcrumbs'][] = ['label' => AmosDiscussioni::t('amosdiscussioni', Yii::$app->session->get('previousTitle')), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discussioni-topic-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
