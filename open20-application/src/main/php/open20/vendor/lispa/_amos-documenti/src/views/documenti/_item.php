<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti
 * @category   CategoryName
 */

use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\forms\ItemAndCardHeaderWidget;
use lispa\amos\core\forms\PublishedByWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\views\toolbars\StatsToolbar;
use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\notificationmanager\forms\NewsWidget;
?>

<div class="listview-container documents">
    <div class="post-horizontal">
        <?php
        $creatoreDocumenti = $model->getCreatedUserProfile()->one();
        $dataPubblicazione = Yii::$app->getFormatter()->asDatetime($model->created_at);
        $nomeCreatoreDocumenti = AmosDocumenti::tHtml('amosdocumenti', 'Utente Cancellato');
        ?>

        <div class="col-sm-7 col-xs-12 nop">
            <div class="col-xs-12 nop">
                <?= ItemAndCardHeaderWidget::widget([
                    'model' => $model,
                    'publicationDateField' => 'created_at',
                ]);

                $document = $model->getDocumentMainFile();

                if ($document):
                    $classContainer = 'col-sm-7 col-xs-12';
                else:
                    $classContainer = 'col-xs-12';
                endif;
                ?>
            </div>
        </div>
        <div class="<?= $classContainer ?> nop">
            <div class="post-content col-xs-12 nop">
                <div class="post-title col-xs-10">
                    <a href="/documenti/documenti/view?id=<?= $model->id ?>">
                        <h2><?= $model->titolo ?></h2>
                    </a>
                </div>
                <?php
                echo NewsWidget::widget([
                    'model' => $model,
                ]);
                ?>
                <?= ContextMenuWidget::widget([
                    'model' => $model,
                    'actionModify' => "/documenti/documenti/update?id=" . $model->id,
                    'actionDelete' => "/documenti/documenti/delete?id=" . $model->id,
                    'modelValidatePermission' => 'DocumentValidate',
                    'mainDivClasses' => 'col-xs-1 nop'
                ]) ?>
                <div class="clearfix"></div>
                <div class="row nom post-wrap">
                    <div class="post-text col-xs-12">
                        <p>
                            <?= $model->descrizione_breve ?>
                            <a class="underline" href=/documenti/documenti/view?id=<?= $model->id ?>><?= AmosDocumenti::tHtml('amosdocumenti', 'Leggi tutto') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar col-sm-5 col-xs-12">
            <div class="container-sidebar">
                <?php
                if ($document) : ?>
                    <div class="box">
                        <?= '<span class="icon">' . AmosIcons::show('download-general', ['class' => 'am-4'], 'dash') . '</span><p class="title">' . $document->name . '.' . $document->type . '</p>'; ?>
                    </div>
                    <div class="box post-info">
                        <?= PublishedByWidget::widget([
                            'model' => $model,
                            'layout' => '{publisher}{targetAdv}{category}' . (Yii::$app->user->can('ADMIN') ? '{status}' : '')
                        ]) ?>
                        <p><strong><?= ($model->primo_piano) ? AmosDocumenti::tHtml('amosdocumenti', 'Pubblicato in prima pagina') : '' ?></strong></p>
                    </div>
                    <div class="footer_sidebar col-xs-12 nop">
                        <?= Html::a(AmosDocumenti::tHtml('amosdocumenti', 'Scarica file'), ['/attachments/file/download/', 'id' => $document->id, 'hash' => $document->hash], [
                            'title' => AmosDocumenti::t('amosdocumenti', 'Scarica file'),
                            'class' => 'bk-btnImport pull-right btn btn-amministration-primary',
                        ]); ?>
                        <?php
                        //$statsToolbar = $model->getStatsToolbar();
                        $visible = isset($statsToolbar) ? $statsToolbar : false;
                        if ($visible) {
                            echo StatsToolbar::widget([
                                'model' => $model,
                            ]);
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
