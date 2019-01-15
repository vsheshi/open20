<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

/**
 * @var View $this
 * @var ActiveDataProvider $listaTopic
 * @var WidgetGraphicsUltimeDiscussioni $widget
 * @var string $toRefreshSectionId
 */

use lispa\amos\core\forms\WidgetGraphicsActions;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\discussioni\models\DiscussioniTopic;
use lispa\amos\discussioni\widgets\graphics\WidgetGraphicsUltimeDiscussioni;
use lispa\amos\discussioni\assets\ModuleDiscussioniInterfacciaAsset;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

$moduleDiscussioni = \Yii::$app->getModule(AmosDiscussioni::getModuleName());
ModuleDiscussioniInterfacciaAsset::register($this);
?>

<div class="grid-item grid-item--height2">
<div class="box-widget latest-discussions">
    <div class="box-widget-toolbar">
        <h2 class="box-widget-title col-xs-10 nop"><?= AmosDiscussioni::tHtml('amosdiscussioni', 'Ultime discussioni') ?></h2>
        <?php if(isset($moduleDiscussioni) && !$moduleDiscussioni->hideWidgetGraphicsActions) { ?>
            <?= WidgetGraphicsActions::widget([
                'widget' => $widget,
                'tClassName' => AmosDiscussioni::className(),
                'actionRoute' => '/discussioni/discussioni-topic/create',
                'toRefreshSectionId' => $toRefreshSectionId
            ]);?>
        <?php } ?>
    </div>
    <section>
        <?php Pjax::begin(['id' => $toRefreshSectionId]); ?>
        <div role="listbox">
            <?php
            if (count($listaTopic->getModels()) == 0):
                $textReadAll =  AmosDiscussioni::t('amosdiscussioni', '#addDiscussions');
                $linkReadAll = ['/discussioni/discussioni-topic-wizard/introduction'];
            ?>

                <div class="list-items list-empty clearfixplus"><h2 class="box-widget-subtitle"><?= AmosDiscussioni::tHtml('amosdiscussioni', 'Nessuna Discussione'); ?></h2></div>
            <?php else:
                $textReadAll =  AmosDiscussioni::t('amosdiscussioni', 'Visualizza Elenco Discussioni');
                $linkReadAll = ['/discussioni'];
                ?>
                <div class="list-items clearfixplus">
                    <?php
                    foreach ($listaTopic->getModels() as $topic):
                        /** @var DiscussioniTopic $topic */
                        ?>
                        <div class="col-xs-12 widget-listbox-option" role="option">
                            <article class="col-xs-12 nop">
                                <div class="container-img">
                                    <?php
                                    $url = '/img/img_default.jpg';
                                    if (!is_null($topic->discussionsTopicImage)) {
                                        $url = $topic->discussionsTopicImage->getUrl('square_small',false,true);
                                    }
                                    ?>
                                    <?= Html::img($url, ['class' => 'img-responsive', 'alt' => AmosDiscussioni::t('amosdiscussioni', 'Immagine della discussione')]); ?>
                                </div>
                                <div class="container-text">
                                    <div class="col-xs-12 nop listbox-date">
                                        <p><?= Yii::$app->getFormatter()->asDatetime($topic->created_at); ?></p>
                                        <h2 class="box-widget-subtitle">
                                            <?php
                                            $decode_titolo = strip_tags($topic->titolo);
                                            if (strlen($decode_titolo) > 50) {
                                                $stringCut = substr($decode_titolo, 0, 50);
                                                echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                            } else {
                                                echo $decode_titolo;
                                            }
                                            ?>
                                        </h2>
                                    </div>
                                    <!-- <div class="col-xs-12"> -->
                                    <!--  <p class="box-widget-text"> -->
                                    <?php
                                    /*if (strlen($topic->testo) > 120) {
                                        $stringCut = substr($topic->testo, 0, 120);
                                        echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                    } else {
                                        echo $topic->testo;
                                    }*/
                                    ?>
                                    <!--  </p> -->
                                    <!-- </div> -->
                                </div>
                            </article>
                            <div class="col-xs-12 footer-listbox nop">
                                <p class="pull-left"><?= AmosIcons::show("comment", [
                                        'class' => 'am-1'
                                    ])
                                    ?>
                                    <?php
                                    if ($topic->getDiscussioniRisposte()->count() > 1) :
                                        $stringContr =  AmosDiscussioni::tHtml('amosdiscussioni', 'contributi');
                                    else:
                                        $stringContr =  AmosDiscussioni::tHtml('amosdiscussioni', 'contributo');
                                    endif;
                                    ?>
                                    <?= ($numeroContributi = $topic->getDiscussionComments()->count()) ? $numeroContributi . ' ' . $stringContr : AmosDiscussioni::tHtml('amosdiscussioni', 'Nessun contributo'); ?></p>

                                <!--                                <span class="">-->
                                <!--                                    < ?= Html::a(AmosDiscussioni::t('amosdiscussioni', 'LEGGI TUTTO'), ['../discussioni/discussioni-topic/index']); ?>-->
                                <!--                                </span>-->
                                <span class="pull-right">
                                    <?= Html::a(AmosDiscussioni::t('amosdiscussioni', 'CONTRIBUISCI'), ['../discussioni/discussioni-topic/partecipa', 'id' => $topic->id], ['class' => 'btn btn-navigation-primary']); ?>
                                    </span>
                            </div>
                        </div>
                        <?php
                    endforeach;
                    ?>
                </div>
                <?php
            endif;
            ?>
        </div>
        <?php Pjax::end(); ?>
    </section>
    <div class="read-all"><?= Html::a($textReadAll, $linkReadAll, ['class' => '']); ?></div>
</div>
</div>