<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\news
 * @category   CategoryName
 */

use lispa\amos\attachments\components\AttachmentsTableWithPreview;
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\forms\ItemAndCardHeaderWidget;
use lispa\amos\core\forms\PublishedByWidget;
use lispa\amos\core\forms\ShowUserTagsWidget;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\views\toolbars\StatsToolbar;
use lispa\amos\news\AmosNews;

/**
 * @var yii\web\View $this
 * @var lispa\amos\news\models\News $model
 */

$this->title = $model->titolo;

$ruolo = Yii::$app->authManager->getRolesByUser(Yii::$app->getUser()->getId());
if (isset($ruolo['ADMIN'])) {
    $url = ['index'];
}

// Tab ids
$idTabCard = 'tab-card';
$idClassifications = 'tab-classifications';
$idTabAttachments = 'tab-attachments';

/** @var \lispa\amos\news\controllers\NewsController $controller */
$controller = Yii::$app->controller;
$controller->setNetworkDashboardBreadcrumb();
$this->params['breadcrumbs'][] = ['label' => AmosNews::t('amosnews', Yii::$app->session->get('previousTitle')), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = '';

$hidePubblicationDate = Yii::$app->controller->newsModule->hidePubblicationDate;
?>

<div class="news-view post-details col-xs-12">
    <?php $this->beginBlock('card'); ?>
    <div class="post col-xs-12 nop nom">
        <?= ItemAndCardHeaderWidget::widget([
            'model' => $model,
            'publicationDateField' => 'data_pubblicazione'
        ]) ?>
        <div class="post-content col-xs-12 nop">

            <div class="post-title col-xs-10">
                <h2><?= $model->titolo ?></h2>
            </div>
            <?= ContextMenuWidget::widget([
                'model' => $model,
                'actionModify' => "/news/news/update?id=" . $model->id,
                'actionDelete' => "/news/news/delete?id=" . $model->id,
                'labelDeleteConfirm' => AmosNews::t('amosnews', 'Sei sicuro di voler cancellare questa notizia?'),
                'modelValidatePermission' => 'NewsValidate'
            ]) ?>
            <div class="clearfix"></div>
            <?php
            $url = '/img/img_default.jpg';
            if (!is_null($model->newsImage)) {
                $url = $model->newsImage->getUrl('square_large', false, true);
            }
            ?>
            <div class='col-md-5 col-sm-6 col-xs-12 post-image-right'>
                <?= Html::img($url, [
                    'title' => AmosNews::t('amosnews', 'Immagine della notizia'),
                    'class' => 'img-responsive'
                ]); ?>
            </div>
            <div class="post-text">
                <h3 class="nom"><?= $model->sottotitolo ?></h3>
                <?= $model->descrizione ?>
            </div>
        </div>
        <div class="post-info col-xs-12">
            <?php
            $layoutAdmin = '{status}';
            if(!$hidePubblicationDate) {
                $layoutAdmin .= '{pubblicationdates}';
            }?>
            <?= PublishedByWidget::widget([
                'model' => $model,
                'layout' => '{publisherAdv}{targetAdv}{category}' . (Yii::$app->user->can('ADMIN') ? $layoutAdmin : '')
            ]) ?>
            <?= ($model->primo_piano) ? '<p><strong>' . AmosNews::t('amosnews', 'Pubblicato in prima pagina') . '</strong></p>' : '' ?>
        </div>
        <div class="shared col-xs-12 nop">
            <?php
                echo StatsToolbar::widget([
                    'model' => $model,
                    'onClick' => true
                ]);
            ?>
        </div>
    </div>
    <?php $this->endBlock(); ?>

    <?php
    $itemsTab[] = [
        'label' => AmosNews::t('amosnews', 'Scheda'),
        'content' => $this->blocks['card'],
        'options' => ['id' => $idTabCard],
    ];
    ?>

    <?php if (Yii::$app->getModule('tag')): ?>
        <?php $this->beginBlock($idClassifications); ?>
        <div class="">
            <?= ShowUserTagsWidget::widget([
                'userProfile' => $model->id,
                'className' => $model->className()
            ]);
            ?>
        </div>
        <?php $this->endBlock(); ?>
        <?php
        $itemsTab[] = [
            'label' => AmosNews::t('amosnews', 'Tag aree di interesse'),
            'content' => $this->blocks[$idClassifications],
            'options' => ['id' => $idClassifications],
        ];
        ?>
    <?php endif; ?>

    <?php $this->beginBlock('attachments'); ?>
    <div class="allegati col-xs-12 nop">
        <!-- TODO sostituire il tag h3 con il tag p e applicare una classe per ridimensionare correttamente il testo per accessibilità -->
        <h3><?= AmosNews::tHtml('amosnews', 'Allegati') ?></h3>
        <?= AttachmentsTableWithPreview::widget([
            'model' => $model,
            'attribute' => 'attachments',
            'viewDeleteBtn' => false
        ]) ?>
    </div>
    <?php $this->endBlock(); ?>

    <?php
    $itemsTab[] = [
        'label' => AmosNews::t('amosnews', 'Allegati'),
        'content' => $this->blocks['attachments'],
        'options' => ['id' => $idTabAttachments],
    ];
    ?>

    <?= Tabs::widget(
        [
            'encodeLabels' => false,
            'items' => $itemsTab
        ]
    );
    ?>
</div>
