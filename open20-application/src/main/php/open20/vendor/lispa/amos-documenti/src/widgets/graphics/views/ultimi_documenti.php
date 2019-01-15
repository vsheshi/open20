<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\widgets\graphics\views
 * @category   CategoryName
 */

/**
 * @var View $this
 * @var ActiveDataProvider $listaDocumenti
 * @var WidgetGraphicsUltimeDocumenti $widget
 * @var string $toRefreshSectionId
 */

use lispa\amos\core\forms\WidgetGraphicsActions;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\documenti\models\Documenti;
use lispa\amos\documenti\widgets\graphics\WidgetGraphicsUltimeDocumenti;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;
use lispa\amos\documenti\assets\ModuleDocumentiAsset;
ModuleDocumentiAsset::register($this);
$moduleDocumenti = \Yii::$app->getModule(AmosDocumenti::getModuleName());
?>
<div class="document grid-item grid-item--width2">
<div class="box-widget latest-documents">
    <div class="box-widget-toolbar">
        <h1 class="box-widget-title col-xs-10 nop"><?= AmosDocumenti::tHTml('amosdocumenti', 'Documenti') ?></h1>
        <?php if(isset($moduleDocumenti) && !$moduleDocumenti->hideWidgetGraphicsActions) { ?>
            <?= WidgetGraphicsActions::widget([
                'widget' => $widget,
                'tClassName' => AmosDocumenti::className(),
                'actionRoute' => '/documenti/documenti/create',
                'toRefreshSectionId' => $toRefreshSectionId
            ]);
        }?>
    </div>
    <section>
        <h2 class="sr-only"><?= AmosDocumenti::t('amosdocumenti', 'Ultimi documenti') ?></h2>
        <div class="">
        <?php Pjax::begin(['id' => $toRefreshSectionId]); ?>
        <div role="listbox">
            <?php
            if (count($listaDocumenti->getModels()) == 0):
                $textReadAll =  AmosDocumenti::t('amosdocumenti', '#addDocument');
                $linkReadAll = ['/documenti/documenti-wizard/introduction'];
            ?>
            <div class="list-items list-empty">
                <h2 class="box-widget-subtitle"><?= AmosDocumenti::tHtml('amosdocumenti', 'Nessun documento'); ?></h2>
            </div>
            <?php
            else:
                $textReadAll =  AmosDocumenti::t('amosdocumenti', 'Visualizza Tutti');
                $linkReadAll = ['/documenti'];
            ?>
            <div class="list-items">
                <?php
                foreach ($listaDocumenti->getModels() as $documenti):
                    /** @var Documenti $documenti */
                    ?>
                    <div class="widget-listbox-option" role="option">
                        <article class="col-xs-12 nop">
                            <div class="container-icon">
                                <?= AmosIcons::show('download-general',['class' => 'icon_widget_graph'], 'dash') ?>
                            </div>
                            <div class="container-text">
                                <div class="col-xs-12 listbox-date nop">
                                    <p><?= Yii::$app->getFormatter()->asDatetime($documenti->created_at); ?></p>
                                    <h2 class="box-widget-subtitle">
                                        <?php
                                        if (strlen($documenti->titolo) > 50) {
                                            $stringCut = substr($documenti->titolo, 0, 50);
                                            echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                        } else {
                                            echo $documenti->titolo;
                                        }
                                        ?>
                                    </h2>
                                </div>
<!--                                <div class="col-xs-12 nopl">-->
<!--                                    <p class="box-widget-text">< ?= $documenti->sottotitolo; ?></p>-->
<!--                                </div>-->
                            </div>
                             <div class="col-xs-12 footer-listbox nop">
                                <span>
                                    <?= Html::a(AmosDocumenti::t('amosdocumenti', 'VISUALIZZA'), ['/documenti/documenti/view', 'id' => $documenti->id], ['class' => 'btn btn-navigation-primary']); ?>
                                </span>
                            </div>
                        </article>
                    </div>
                    <?php
                endforeach;
                ?>
                </div>

                <?php
            endif;
            ?>
        </div>
        </div>
        <?php Pjax::end(); ?>
    </section>
    <div class="col-xs-12 read-all"><?= Html::a($textReadAll, $linkReadAll, ['class' => '']); ?></div>
</div>
</div>
