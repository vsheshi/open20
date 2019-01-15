<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti
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
use lispa\amos\documenti\AmosDocumenti;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var lispa\amos\documenti\models\Documenti $model
 */

$this->title = $model->titolo;
$ruolo = Yii::$app->authManager->getRolesByUser(Yii::$app->getUser()->getId());
if (isset($ruolo['ADMIN'])) {
    $url = ['index'];
}

/** @var \lispa\amos\documenti\controllers\DocumentiController $controller */
$controller = Yii::$app->controller;
$hidePubblicationDate = $controller->documentsModule->hidePubblicationDate;
$controller->setNetworkDashboardBreadcrumb();
$this->params['breadcrumbs'][] = ['label' => AmosDocumenti::t('amosdocumenti', Yii::$app->session->get('previousTitle')), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

// Tab ids
$idTabCard = 'tab-card';
$idClassifications = 'tab-classifications';
$idTabAttachments = 'tab-attachments';

$select2Id = 'all-document-versions-id';

$js = "
$('#" . $select2Id . "').on('change', function(e) {
    e.preventDefault();
    var selectedValue = $(this).val();
    window.location.href = '/documenti/documenti/view?id=' + selectedValue;
});
";
$this->registerJs($js, View::POS_READY);

$hidePubblicationDate = $controller->documentsModule->hidePubblicationDate;
?>

<div class="documenti-view post-horizontal documents col-xs-12 nop">
    <?php $this->beginBlock('card'); ?>

    <?php
    $creatoreDocumenti = $model->getCreatedUserProfile()->one();
    $nomeCreatoreDocumenti = $creatoreDocumenti->nome . " " . $creatoreDocumenti->cognome;
    $dataPubblicazione = Yii::$app->getFormatter()->asDatetime($model->created_at);
    $avatarCreatore = $creatoreDocumenti->avatar_id;
    ?>
    <?= ItemAndCardHeaderWidget::widget([
        'model' => $model,
        'publicationDateField' => 'created_at'
    ]) ?>
    <?php
    $classContainer = 'col-xs-12';
    $documentMainFile = $model->getDocumentMainFile();
    if ($documentMainFile) {
        $classContainer = 'col-sm-7 col-xs-12';
    }
    ?>
    <div class="<?= $classContainer ?> nop">
        <div class="post-content col-xs-12 nop">
            <div class="post-title col-xs-10">
                <h2><?= (strlen($model->titolo) > 80) ?  substr($model->titolo,0,75).'[...]' : $model->titolo ?></h2>
            </div>
            <?= ContextMenuWidget::widget([
                'model' => $model,
                'actionModify' => "/documenti/documenti/update?id=" . $model->id,
                'actionDelete' => "/documenti/documenti/delete?id=" . $model->id,
                'modelValidatePermission' => 'DocumentValidate',
                'mainDivClasses' => 'col-xs-1 nop',
                'disableModify' => $controller->documentsModule->enableDocumentVersioning
            ]) ?>
            <div class="clearfix"></div>
            <div class="row nom post-wrap">
                <div class="post-text col-xs-12">
                    <h3 class="subtitle"><?= $model->sottotitolo ?></h3>
                    <?= $model->descrizione ?>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar col-sm-5 col-xs-12">
        <div class="container-sidebar">
            <?php if ($controller->documentsModule->enableDocumentVersioning): ?>
                <div class="box">
                    <?= Select2::widget([
                        'model' => $model,
                        'attribute' => 'version',
                        'data' => ArrayHelper::map($model->allDocumentVersions, 'id', 'versionInfo'),
                        'options' => [
                            'placeholder' => AmosDocumenti::t('amosdocumenti', 'Cambia versione'),
                            'id' => $select2Id,
                            'lang' => substr(Yii::$app->language, 0, 2),
                            'multiple' => false,
                            'value' => $model->id
                        ],
                        'addon' => [
                            'prepend' => $model->getAttributeLabel('version')
                        ]
                    ]) ?>
                </div>
            <?php endif; ?>
            <?php if ($documentMainFile): ?>
                <div class="box">
                    <?= '<span class="icon">' . AmosIcons::show('download-general', ['class' => 'am-4'], 'dash') . '</span><p class="title">' . ((strlen($documentMainFile->name)>80)? substr($documentMainFile->name,0,75).'[...]' : $documentMainFile->name) . '.' . $documentMainFile->type . '</p>'; ?>
                </div>
                <div class="box post-info">
                    <?php
                    $moduleDocumenti = \Yii::$app->getModule(AmosDocumenti::getModuleName());
                    if(\Yii::$app->user->can('ADMIN')) {
                        $layoutPublishedByWidget = $moduleDocumenti->layoutPublishedByWidget['layoutAdmin'];
                    }
                    else {
                        $layoutPublishedByWidget = $moduleDocumenti->layoutPublishedByWidget['layout'];
                    }
                   ?>
                    <?= PublishedByWidget::widget([
                        'model' => $model,
                        'layout' => $layoutPublishedByWidget
                    ]) ?>
                    <p>
                        <strong><?= ($model->primo_piano) ? AmosDocumenti::tHtml('amosdocumenti', 'Pubblicato in prima pagina') : '' ?></strong>
                    </p>
                </div>
                <div class="footer_sidebar col-xs-12 nop">
                    <?= Html::a(AmosDocumenti::tHtml('amosdocumenti', 'Scarica file'), ['/attachments/file/download/', 'id' => $documentMainFile->id, 'hash' => $documentMainFile->hash], [
                        'title' => AmosDocumenti::t('amosdocumenti', 'Scarica file'),
                        'class' => 'bk-btnImport pull-right btn btn-amministration-primary',
                    ]); ?>
                    <?php
                    $statsToolbar = $model->getStatsToolbar();
                    $visible = isset($statsToolbar) ? $statsToolbar : false;
                    if ($visible) {
                        echo StatsToolbar::widget([
                            'model' => $model,
                            'onClick' => true
                        ]);
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php $this->endBlock(); ?>

    <?php
    $itemsTab[] = [
        'label' => AmosDocumenti::tHtml('amosdocumenti', 'Scheda'),
        'content' => $this->blocks['card'],
        'options' => ['id' => $idTabCard],
    ];
    ?>

    <?php if (Yii::$app->getModule('tag')): ?>
        <?php $this->beginBlock($idClassifications); ?>
        <div class="body">
            <?= ShowUserTagsWidget::widget([
                'userProfile' => $model->id,
                'className' => $model->className()
            ]);
            ?>
        </div>
        <?php $this->endBlock(); ?>
        <?php
        $itemsTab[] = [
            'label' => AmosDocumenti::tHtml('amosdocumenti', 'Tag'),
            'content' => $this->blocks[$idClassifications],
            'options' => ['id' => $idClassifications],
        ];
        ?>
    <?php endif; ?>

    <?php $this->beginBlock('attachments'); ?>
    <div class="allegati col-xs-12 nop">
        <?php if (count($model->getDocumentAttachments()) == 0): ?>
            <p><h4><?= AmosDocumenti::tHtml('amosdocumenti', 'Nessun allegato presente') ?></h4></p>
        <?php else: ?>
            <p>
            <h3><?= AmosDocumenti::tHtml('amosdocumenti', 'Allegati') ?></h3>
            </p>
            <?= AttachmentsTableWithPreview::widget([
                'model' => $model,
                'attribute' => 'documentAttachments',
                'viewDeleteBtn' => false
            ]) ?>
        <?php endif; ?>
    </div>
    <?php $this->endBlock(); ?>

    <?php
    $itemsTab[] = [
        'label' => AmosDocumenti::tHtml('amosdocumenti', 'Allegati'),
        'content' => $this->blocks['attachments'],
        'options' => ['id' => $idTabAttachments],
    ];
    ?>

    <?= Tabs::widget([
        'encodeLabels' => false,
        'items' => $itemsTab
    ]); ?>
</div>
