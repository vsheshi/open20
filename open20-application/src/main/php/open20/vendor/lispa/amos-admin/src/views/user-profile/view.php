<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\forms\Tabs;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;

/**
 * @var yii\web\View $this
 * @var lispa\amos\admin\models\UserProfile $model
 */

$this->title = $model;
$this->params['breadcrumbs'][] = ['label' => AmosAdmin::t('amosadmin', 'Utenti'), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => AmosAdmin::t('amosadmin', 'Elenco'), 'url' => ['index']];
$this->params['breadcrumbs'][] = '';

\lispa\amos\admin\assets\AmosAsset::register($this);

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;
$idTabAdministration = 'tab-administration';

$enableUserContacts = AmosAdmin::getInstance()->enableUserContacts;

?>

<div class="details_card">
    <div class="profile">
        <div class="col-sm-4 col-md-3 col-xs-12 left-column">
            <div class="img-profile">
                <?php if (($adminModule->confManager->isVisibleBox('box_foto', ConfigurationManager::VIEW_TYPE_VIEW)) &&
                    ($adminModule->confManager->isVisibleField('userProfileImage',
                        ConfigurationManager::VIEW_TYPE_VIEW))
                ): ?>
                    <?php
                    $url = $model->getAvatarUrl('original');
                    Yii::$app->imageUtility->methodGetImageUrl = 'getAvatarUrl';
                    try {
                        $getHorizontalImageClass = Yii::$app->imageUtility->getHorizontalImage($model->userProfileImage)['class'];
                        $getHorizontalImageMarginLeft = 'margin-left:' . Yii::$app->imageUtility->getHorizontalImage($model->userProfileImage)["margin-left"] . 'px;margin-top:' . Yii::$app->imageUtility->getHorizontalImage($model->userProfileImage)["margin-top"] . 'px;';
                    } catch (\Exception $ex) {
                        $getHorizontalImageClass = '';
                        $getHorizontalImageMarginLeft = '';
                    }
                    ?>
                    <?= Html::img($url, [
                        'class' => 'img-responsive ' . $getHorizontalImageClass,
                        'style' => $getHorizontalImageMarginLeft,
                        'alt' => AmosAdmin::t('amosadmin', 'Immagine del profilo')
                    ]); ?>
                <?php endif; ?>
                <div class="under-img">
                    <?php if ($enableUserContacts && Yii::$app->user->id != $model->user_id): ?>
                        <?= \lispa\amos\admin\widgets\ConnectToUserWidget::widget([
                            'model' => $model,
                            'isProfileView' => true
                        ]) ?>
                    <?php endif; ?>

                    <?php if ($adminModule->confManager->isVisibleField('nome',
                        ConfigurationManager::VIEW_TYPE_VIEW)): ?>
                        <?php if ($model->nome): ?>
                            <h2><?= $model->nome ?>
                                <br class="hidden-xs"/>
                                <?php if ($adminModule->confManager->isVisibleField('cognome',
                                    ConfigurationManager::VIEW_TYPE_VIEW)): ?>
                                    <?= ($model->cognome ? $model->cognome : '') ?>
                                <?php endif; ?>
                            </h2>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="container-info-icons">
                <?php
                if ($model->validato_almeno_una_volta) {
                    $color = "grey";
                    $title = AmosAdmin::t('amosadmin', 'Profile Active');
                    if ($model->status == \lispa\amos\admin\models\UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED) {
                        $color = "green";
                        $title = AmosAdmin::t('amosadmin', 'Profile Validated');
                    }
                    //TODO replace check-all with cockade
                    echo Html::tag('div', AmosIcons::show('check-all',
                            ['class' => 'am-2 ', 'style' => 'color: ' . $color]) . " " . $title);
                    $facilitatorUserIds = Yii::$app->getAuthManager()->getUserIdsByRole("FACILITATOR");
                    if (in_array($model->user_id, $facilitatorUserIds)) {
                        //TODO replace account with man dressing tie and jacket
                        $facilitatorIcon = AmosIcons::show('account', [
                            'class' => 'am-2',
                            'style' => 'color: green',
                            'title' => AmosAdmin::t('amosadmin', 'Facilitator')
                        ]);
                        echo Html::tag('div', $facilitatorIcon . "&nbsp;" . AmosAdmin::t('amosadmin', 'Facilitator'));
                    }
                }
                ?>
            </div>

            <div class="container-info-icons">
                <?php
                if ($model->user_id != Yii::$app->user->id && Yii::$app->user->can('IMPERSONATE_USERS')) {
                    echo Html::a(
                            AmosIcons::show('assignment-account', ['class' => 'btn-cancel-search']) . AmosAdmin::t('amosadmin', 'Impersonate'),
                            \Yii::$app->urlManager->createUrl(['/admin/security/impersonate',
                                    'user_id' => $model->user_id
                            ]),
                            ['class' => 'btn btn-secondary']
                    );
                }
                ?>
            </div>
        </div>

        <div class="col-sm-8 col-md-9 col-xs-12 right-column">

            <?php
            $idTabCard = 'tab-card';
            $idTabNetwork = 'tab-network';
            $idClassificazioni = 'tab-classificazioni';
            $idTabSettings = 'tab-settings';

            $nomeCognome = '';
            if ($adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_VIEW)) {
                $nomeCognome .= $model->nome;
            }
            if ($adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_VIEW)) {
                $nomeCognome .= ' ' . $model->cognome;
            }

            // LE TAB
            $this->beginBlock($idTabCard);
            // TAB SCHEDA
            ?>
            <section class="section-data">
                <div class="row">
                    <?php if (strlen($nomeCognome) > 0): ?>
                        <h2 class="col-xs-10 nop">
                            <?= $nomeCognome ?>
                        </h2>
                    <?php endif; ?>
                    <div class="col-xs-2">
                        <?= ContextMenuWidget::widget([
                            'model' => $model,
                            'actionModify' => "/admin/user-profile/update?id=" . $model->id,
                            'disableDelete' => true
                        ]) ?>
                    </div>
                </div>
                <?php if (
                    ($adminModule->confManager->isVisibleBox('box_informazioni_base',
                        ConfigurationManager::VIEW_TYPE_VIEW)) &&
                    ($adminModule->confManager->isVisibleField('presentazione_breve',
                        ConfigurationManager::VIEW_TYPE_VIEW))
                    && $model->presentazione_breve
                ): ?>
                    <div class="row">
                        <?= $model->presentazione_breve ?>
                    </div>
                <?php endif; ?>
                <?php if (
                    ($adminModule->confManager->isVisibleBox('box_presentazione_personale',
                        ConfigurationManager::VIEW_TYPE_VIEW)) &&
                    ($adminModule->confManager->isVisibleField('presentazione_personale',
                        ConfigurationManager::VIEW_TYPE_VIEW))
                    && $model->presentazione_personale
                ): ?>
                    <div class="row">
                        <?= $model->presentazione_personale ?>
                    </div>
                <?php endif; ?>
                <?php if ( $adminModule->confManager->isVisibleBox('box_dati_contatto', ConfigurationManager::VIEW_TYPE_VIEW)): ?>
                    <div class="row">
                        <h2>
                            <?= AmosIcons::show('phone'); ?>
                            <?= AmosAdmin::tHtml('amosadmin', 'Dati di Contatto'); ?>
                        </h2>
                    </div>
                    <?php if ( $adminModule->confManager->isVisibleField('email', ConfigurationManager::VIEW_TYPE_VIEW)): ?>
                        <div class="col-lg-6">
                            <strong><?= AmosAdmin::t('amosadmin', 'Email: ')?></strong><?= $model->user->email ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( $adminModule->confManager->isVisibleField('telefono', ConfigurationManager::VIEW_TYPE_VIEW)): ?>
                        <div class="col-lg-6">
                            <strong><?= AmosAdmin::t('amosadmin', 'Telefono: ')?></strong><?= !empty($model->telefono) ? $model->telefono : AmosAdmin::tHtml('amosadmin', 'Non presente') ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $adminModule->confManager->isVisibleField('email_pec', ConfigurationManager::VIEW_TYPE_VIEW)): ?>
                        <div class="col-lg-6">
                            <strong><?= AmosAdmin::t('amosadmin', 'Email PEC: ')?></strong><?= $model->email_pec ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (
                    ($adminModule->confManager->isVisibleBox('box_prevalent_partnership',
                        ConfigurationManager::VIEW_TYPE_VIEW)) &&
                    ($adminModule->confManager->isVisibleField('prevalent_partnership_id',
                        ConfigurationManager::VIEW_TYPE_VIEW))
                ): ?>
                    <br>
                    <div class="row">
                        <div>
                            <h2>
                                <?= AmosIcons::show('case'); ?>
                                <?= AmosAdmin::t('amosadmin', 'Partenership') ?></h2>
                            <?php if (!is_null($model->prevalentPartnership)){ ?>
                                <div class="col-xs-3">
                                    <div class="img-profile">
                                        <?php
                                        // TODO da modificare quando ci sarà terminato il nuovo plugin organizzazioni
                                        $url = '/img/img_default.jpg';
                                        if (isset($model->prevalentPartnership) && isset($model->prevalentPartnership->logoOrganization)) {
                                            $url = $model->prevalentPartnership->logoOrganization->getUrl('square_medium', false, true);
                                        }
                                        echo Html::img($url, ['class' => 'img-responsive']);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div><strong><?= $model->prevalentPartnership->name ?></strong></div>
                                    <!-- TODO tipologia organizzazione quando sarà presente -->
                                    <!-- TODO referente operativo quando sarà presente -->
                                </div>
                            <?php } else { ?>
                                <div class="col-xs-7">
                                    <div><?= AmosAdmin::tHtml('amosadmin',
                                            'Prevalent partnership not specified') ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
            <?php $this->endBlock(); ?>

            <?php
                //Tab rete
                $this->beginBlock($idTabNetwork);
                $moduleCwh = Yii::$app->getModule('cwh');
                if ($enableUserContacts && $model->validato_almeno_una_volta) {
                    echo \lispa\amos\admin\widgets\UserContacsWidget::widget([
                        'userId' => $model->user_id,
                        'isUpdate' => false
                    ]);
                }
                if (isset($moduleCwh)) {
                    echo \lispa\amos\cwh\widgets\UserNetworkWidget::widget([
                        'userId' => $model->user_id,
                        'isUpdate' => false
                    ]);
                }
                $this->endBlock();
            ?>

            <!-- TODO per uso futuro -->
            <?php //$this->beginBlock($idTabSettings); ?>
            <!--<div class="body">-->
            <!--</div>-->
            <?php //$this->endBlock(); ?>

            <?php
            //TB CLASSIFICAZIONI
            if (\Yii::$app->getModule('tag')):
                $this->beginBlock($idClassificazioni);
                ?>

                    <?php
                    // now tags displayed in the same way of content models(eg, news, discussioni, ..)
                    //                    echo \lispa\amos\core\forms\ShowUserAreeInteresseWidget::widget([
                    echo \lispa\amos\core\forms\ShowUserTagsWidget::widget([
                        'userProfile' => $model->id,
                        'className' => $model->className()
                    ]);
                    ?>

                <?php
                $this->endBlock();
            endif;
            ?>

            <?php
            $itemsTab[] = [
                'label' => AmosAdmin::t('amosadmin', 'Card'),
                'content' => $this->blocks[$idTabCard],
                'options' => ['id' => $idTabCard]
            ];

                $itemsTab[] = [
                    'label' => AmosAdmin::t('amosadmin', 'Network'),
                    'content' => $this->blocks[$idTabNetwork],
                    'options' => ['id' => $idTabNetwork]
                ];
            ?>
            <?php if (Yii::$app->user->can('PRIVILEGES_MANAGER')): ?>

                <?php $privilegesModule = Yii::$app->getModule('privileges'); ?>
                <?php if (!empty($privilegesModule)) : ?>

                    <?php $this->beginBlock($idTabAdministration); ?>

                    <?= \lispa\amos\privileges\widgets\UserPrivilegesWidget::widget(['userId' => $model->user_id]) ?>

                    <div class="clearfix"></div>
                    <?php $this->endBlock(); ?>

                    <?php
                    $itemsTab[] = [
                        'label' => AmosAdmin::t('amosadmin', 'Administration'),
                        'content' => $this->blocks[$idTabAdministration],
                        'options' => ['id' => $idTabAdministration],
                    ];
                    ?>

                <?php endif; ?>

            <?php endif; ?>

            <?php
            if (\Yii::$app->getModule('tag')) {
                $itemsTab[] = [
                    'label' => AmosAdmin::t('amosadmin', 'Tag aree di interesse'),
                    'content' => $this->blocks[$idClassificazioni],
                    'options' => ['id' => $idClassificazioni]
                ];
            }
            ?>

            <?= Tabs::widget([
                'encodeLabels' => false,
                'items' => $itemsTab
            ]); ?>

            <!--div class="btnViewContainer pull-right">
                < ?= Html::a(AmosAdmin::t('amosadmin', 'Chiudi'), Url::previous(), ['class' => 'btn btn-secondary']); ?>
            </div-->
        </div>
    </div>
</div>
