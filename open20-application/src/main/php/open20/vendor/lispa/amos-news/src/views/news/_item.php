<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\news\views\news
 * @category   CategoryName
 */

use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\forms\ItemAndCardHeaderWidget;
use lispa\amos\core\forms\PublishedByWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\views\toolbars\StatsToolbar;
use lispa\amos\news\AmosNews;
use lispa\amos\notificationmanager\forms\NewsWidget;

/**
 * @var \lispa\amos\news\models\News $model
 */
?>

<div class="listview-container news-item grid-item nop">
    <div class="post col-xs-12">
        <?= ItemAndCardHeaderWidget::widget([
                'model' => $model,
                'publicationDateField' => 'data_pubblicazione'
            ]
        ) ?>
        <div class="post-content col-xs-12 nop">
            <div class="post-title col-xs-10">
                <?= Html::a(Html::tag('h2', $model->titolo), $model->getFullViewUrl()) ?>
            </div>
            <?= NewsWidget::widget(['model' => $model]); ?>
            <?= ContextMenuWidget::widget([
                'model' => $model,
                'actionModify' => "/news/news/update?id=" . $model->id,
                'actionDelete' => "/news/news/delete?id=" . $model->id,
                'labelDeleteConfirm' => AmosNews::t('amosnews', 'Sei sicuro di voler cancellare questa notizia?'),
                'modelValidatePermission' => 'NewsValidate'
            ]) ?>
            <div class="post-image col-xs-12">
                <?php
                $url = '/img/img_default.jpg';
                if (!is_null($model->newsImage)) {
                    $url = $model->newsImage->getUrl('square_medium',false, true);
                }
                $contentImage = Html::img($url, [
                    'class' => 'img-responsive',
                    'alt' => AmosNews::t('amosnews', 'Immagine della notizia')
                ]);
                ?>
                <?= Html::a($contentImage, $model->getFullViewUrl()) ?>
            </div>
            <div class="post-text col-xs-12">
                <p>
                    <?= $model->descrizione_breve ?>
                    <?= Html::a(AmosNews::t('amosnews', 'Leggi tutto'), $model->getFullViewUrl(), ['class' => 'underline']) ?>
                </p>
            </div>
        </div>
        <div class="post-footer col-xs-12 nop">
            <div class="post-info">
                <?= PublishedByWidget::widget([
                    'model' => $model,
                    'layout' => '{publisherAdv}{targetAdv}' . (Yii::$app->user->can('ADMIN') ? '{status}' : '')
                ]) ?>
                <?= ($model->primo_piano) ? '<p><strong>' . AmosNews::t('amosnews', 'Pubblicato in prima pagina') . '</strong></p>' : '' ?>
            </div>
            
            <?php
            $visibale = isset($statsToolbar) ? $statsToolbar : false;
            if ($visibale) {
                echo StatsToolbar::widget([
                    'model' => $model,
                ]);
            }
            ?>

        </div>
        <!--div class="people col-xs-7 ">
            <p><b>20 partecipanti di cui 4 nella tua rete</b></p>

//                    $mediafile = null;
//                    if ($creatoreNews) {
//                        $mediafile = \pendalf89\filemanager\models\Mediafile::findOne($avatarCreatore);
//                    }
//                    if ($mediafile):
//                        echo $mediafile->getThumbImage('small', [
//                            'id' => 'img-avatar_id',
//                            'class' => 'avatar'
//                        ]);
//                        echo $mediafile->getThumbImage('small', [
//                            'id' => 'img-avatar_id',
//                            'class' => 'avatar'
//                        ]);
//                        echo $mediafile->getThumbImage('small', [
//                            'id' => 'img-avatar_id',
//                            'class' => 'avatar'
//                        ]);
//                        echo $mediafile->getThumbImage('small', [
//                            'id' => 'img-avatar_id',
//                            'class' => 'avatar'
//                        ]);
//                    else:
//                        $url = '/img/defaultProfilo.jpg';
//                        echo Html::img($url, ['width' => '50', 'class' => 'avatar' ]);
//                        echo Html::img($url, ['width' => '50', 'class' => 'avatar' ]);
//                        echo Html::img($url, ['width' => '50', 'class' => 'avatar' ]);
//                        echo Html::img($url, ['width' => '50', 'class' => 'avatar' ]);
//                    endif;

    </div-->
    </div>
</div>
