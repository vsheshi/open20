<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core
 * @category   CategoryName
 */

use yii\helpers\Html;

/** @var bool|false $disablePlatformLinks  - if true all the links to dashboard, settings, etc are disabled */
$disablePlatformLinks = isset(Yii::$app->params['disablePlatformLinks']) ? Yii::$app->params['disablePlatformLinks'] : false;

/** @var  $logo */
$logo = isset(Yii::$app->params['logo']) ?
    Html::img(Yii::$app->params['logo'], [
        'class' => 'img-responsive logo-amos',
        'alt' => 'logo ' . Yii::$app->name
    ])
    : '';

/** @var  $signature*/
$signature = isset(Yii::$app->params['logo-signature']) ?
    Html::img(Yii::$app->params['logo-signature'], [
        'class' => 'img-responsive logo-signature',
        'alt' => 'logo ' . Yii::$app->name
    ])
    : '';

$logoUrl = !empty(Yii::$app->params['disablePlatformLinks']) ? null : Yii::$app->homeUrl;
$logoOptions = [];
$title = \lispa\amos\core\module\BaseAmosModule::t('amoscore', 'vai alla home page');
$logoOptions['title'] = $title;

if(!$disablePlatformLinks) {
    $logo = Html::a($logo, $logoUrl, $logoOptions);
    $signature = Html::a($signature, $logoUrl, $logoOptions);
}

?>

<div class="container-logo">
    <div class="container">

        <?php if ( !isset(Yii::$app->params['logo']) && !isset(Yii::$app->params['logo-text']) ) : ?>
            <div class="logo-text text-center">
                <?= (!$disablePlatformLinks) ? Html::a(Yii::$app->name, $logoUrl, $logoOptions) : Html::tag('p',Yii::$app->name); ?>
            </div>
        <?php endif; ?>

        <!-- params logo -->
        <?php if (isset(Yii::$app->params['logo'])): ?>
            <?= $logo; ?>
        <?php endif; ?>

        <!-- params logo-text -->
        <?php if (isset(Yii::$app->params['logo-text']) ): ?>
            <p class="title-text"><?= Yii::$app->params['logo-text'] ?></p>
        <?php endif; ?>

        <!-- params signature logo -->
        <?php if (isset(Yii::$app->params['logo-signature'])): ?>
            <?= $signature ?>
        <?php endif; ?>

    </div>
</div>