<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile\boxes
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use kartik\alert\Alert;

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

/** @var \lispa\amos\admin\models\UserProfile $facilitatorUserProfile */
$facilitatorUserProfile = UserProfile::findOne(['id' => $model->facilitatore_id]);

?>

<?php if ($model->isNewRecord && !$model->facilitatore_id): ?>
    <section>
        <div class="row">
            <div class="col-xs-12">
                <?= Alert::widget([
                    'type' => Alert::TYPE_WARNING,
                    'body' => AmosAdmin::t('amosadmin', 'Before choose the facilitator click on the CREATE button in the bottom to save the profile.'),
                    'closeButton' => false
                ]); ?>
            </div>
        </div>
    </section>
<?php else: ?>
    <section>
        <div class="col-xs-12 facilitator-content">
            <div class="col-xs-12 facilitator-textarea">
                <h4><strong><?= AmosAdmin::t('amosadmin', 'The facilitator') ?></strong></h4>
                <p><?= AmosAdmin::t('amosadmin', 'The facilitator is a user with an in-depth knowledge of the platform\'s objectives and methodology and is responsible for providing assistance to users.') ?></p>
                <p><?= AmosAdmin::t('amosadmin', 'You can contact the facilitator at any time for informations on compiling your profile data and using the platform.') ?></p>
            </div>
            <div class="clearfix"></div>
            <div class="col-xs-12">
                <div class="col-md-6 facilitator-id m-t-15 nop">
                    <?php if (!is_null($facilitatorUserProfile)): ?>
                        <div class="col-xs-4 m-t-5 m-b-15">
                            <?php
                            Yii::$app->imageUtility->methodGetImageUrl = "getAvatarUrl";
                            echo Html::tag('div', Html::img($facilitatorUserProfile->getAvatarUrl(), [
                                'class' => Yii::$app->imageUtility->getRoundImage($facilitatorUserProfile)['class'],
                                'style' => "margin-left: " . Yii::$app->imageUtility->getRoundImage($facilitatorUserProfile)['margin-left'] . "%; margin-top: " . Yii::$app->imageUtility->getRoundImage($facilitatorUserProfile)['margin-top'] . "%;",
                                'alt' => $facilitatorUserProfile->getNomeCognome()
                            ]),
                                ['class' => 'container-round-img-md']);
                            ?>
                        </div>
                        <div class="col-xs-8">
                            <p><strong><?= $facilitatorUserProfile->getNomeCognome() ?></strong></p>
                            <div><?= AmosAdmin::t('amosadmin', 'Prevalent partnership') . ': ' . (!is_null($facilitatorUserProfile->prevalentPartnership) ? $facilitatorUserProfile->prevalentPartnership->name : '-') ?></div>
                            <div><?= Html::a(AmosAdmin::t('amosadmin', 'Change facilitator'), ['/admin/user-profile/associate-facilitator', 'id' => $model->id, 'viewM2MWidgetGenericSearch' => true]) ?></div>
                        </div>
                    <?php else: ?>
                            <div><?= AmosAdmin::tHtml('amosadmin', 'Facilitator not selected') ?></div>
                            <div><?= Html::a(AmosAdmin::t('amosadmin', 'Select facilitator'), ['/admin/user-profile/associate-facilitator', 'id' => $model->id, 'viewM2MWidgetGenericSearch' => true]) ?></div>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                </div>
                <div class="col-xs-12 col-md-6 m-t-15">
                    <div class="col-xs-1 nop text-right">
                        <?= AmosIcons::show('info') ?>
                    </div>
                    <div class="col-xs-11">
                        <?= AmosAdmin::t('amosadmin', 'The platform has automatically assigned you a facilitator, but if you wish, you can change it by choosing a person who knows your professional identity and can help you to value it with suggestions and changes to the informations you entered.') ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
