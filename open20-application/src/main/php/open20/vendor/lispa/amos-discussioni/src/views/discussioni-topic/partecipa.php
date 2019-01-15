<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni\views\discussioni-topic
 * @category   CategoryName
 */

use lispa\amos\admin\widgets\UserCardWidget;
use lispa\amos\attachments\components\AttachmentsTableWithPreview;
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\forms\ItemAndCardHeaderWidget;
use lispa\amos\core\forms\PublishedByWidget;
use lispa\amos\core\forms\ShowUserTagsWidget;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\helpers\Html;
use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\core\views\toolbars\StatsToolbar;

/**
 * @var yii\web\View $this
 * @var lispa\amos\discussioni\models\DiscussioniTopic $model
 * @var yii\widgets\ActiveForm $form
 */

$this->title = $model->titolo;
$this->params['breadcrumbs'][] = ['label' => AmosDiscussioni::t('amosdiscussioni', Yii::$app->session->get('previousTitle')), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = '';

// Tab ids
$idTabCard = 'tab-card';
$idClassifications = 'tab-classifications';
$idTabAttachments = 'tab-attachments';

?>

<div class="discussion  post-details col-xs-12 nop">
    <?php $this->beginBlock('card'); ?>
    <div class="post col-xs-12 nop nom">
        <?= ItemAndCardHeaderWidget::widget([
            'model' => $model,
            'publicationDateField' => 'created_at',
        ]) ?>
        <div class="post-content col-xs-12 nop">
            <div class="post-title col-xs-10">
                <h2><?= $model->titolo ?></h2>
            </div>
            <?= ContextMenuWidget::widget([
                'model' => $model,
                'actionModify' => "/discussioni/discussioni-topic/update?id=" . $model->id,
                'actionDelete' => "/discussioni/discussioni-topic/delete?id=" . $model->id,
                'modelValidatePermission' => 'DiscussionValidate'
            ]) ?>
            <div class="clearfix"></div>
            <div class="row nom post-wrap">
                <?php
                $url = '/img/img_default.jpg';
                if (!is_null($model->discussionsTopicImage)) {
                    $url = $model->discussionsTopicImage->getUrl('original', false, true);
                    ?>
                    <div class="post-image-right nop col-sm-4 col-xs-12">
                        <?= Html::img($url, [
                            'class' => 'img-responsive',
                            'alt' => AmosDiscussioni::t('amosdiscussioni', 'Immagine della discussione')
                        ]); ?>
                    </div>
                    <?php
                }
                ?>
                <div class="post-text">
                    <?= $model->testo ?>
                </div>
            </div>
        </div>

        <div class="post-footer col-xs-12 nop">
            <div class="post-info">
                <?= PublishedByWidget::widget([
                    'model' => $model,
                    'layout' => '{publisher}{targetAdv}' . (Yii::$app->user->can('ADMIN') ? '{status}' : '')
                ]) ?>
            </div>
            <div class="shared">
                <?php
                echo StatsToolbar::widget([
                    'model' => $model,
                    'onClick' => true
                ]);
                ?>
            </div>
            <div class="people col-xs-7 nop">
                <p>
                    <strong>
                        <?php
                        $comments = $model->getDiscussionComments();
                        //numero partecipanti
                        $partecipanti = $comments->groupBy('created_by')->count();

                        if ($partecipanti == 1) {
                            echo $partecipanti . ' ' . AmosDiscussioni::tHtml('amosdiscussioni', 'partecipante');
                        } else {
                            echo $partecipanti . ' ' . AmosDiscussioni::tHtml('amosdiscussioni', 'partecipanti');
                        }
                        // di cui 4 nella tua rete
                        ?>
                    </strong>
                </p>

                <?php
                $participants = $model->commentsUsersAvatars();
                foreach ($participants as $participant) {
                    if ($participant) {
                        echo UserCardWidget::widget(['model' => $participant, 'avatarXS' => true, 'enableLink' => true]);
                    }
                }
                ?>
            </div>
        </div>
        <hr>
    </div>
    <?php $this->endBlock(); ?>
    <?php
    $itemsTab[] = [
        'label' => AmosDiscussioni::t('amosdiscussioni', 'Scheda'),
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
            'label' => AmosDiscussioni::t('amosdiscussioni', 'Tag aree di interesse'),
            'content' => $this->blocks[$idClassifications],
            'options' => ['id' => $idClassifications],
        ];
        ?>
    <?php endif; ?>
    
    <?php $this->beginBlock('attachments'); ?>
    <div class="allegati col-xs-12 nop">
        <div class="allegati col-xs-12 nop">
            <!-- TODO sostituire il tag h3 con il tag p e applicare una classe per ridimensionare correttamente il testo per accessibilità -->
            <h3><?= AmosDiscussioni::tHtml('amosdiscussioni', 'Allegati') ?></h3>
            <?= AttachmentsTableWithPreview::widget([
                'model' => $model,
                'attribute' => 'discussionsAttachments',
                'viewDeleteBtn' => false
            ]) ?>
        </div>
    </div>
    <?php $this->endBlock(); ?>
    <?php
    $itemsTab[] = [
        'label' => AmosDiscussioni::t('amosdiscussioni', 'Allegati'),
        'content' => $this->blocks['attachments'],
        'options' => ['id' => $idTabAttachments],
    ];
    ?>
    <?= Tabs::widget(
        [
            'encodeLabels' => false,
            'items' => $itemsTab
        ]
    ); ?>
</div>
