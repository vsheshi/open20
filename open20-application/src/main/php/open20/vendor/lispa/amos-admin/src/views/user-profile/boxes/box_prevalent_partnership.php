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
use lispa\amos\core\helpers\Html;
use kartik\alert\Alert;
/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

echo \lispa\amos\core\utilities\ModalUtility::createConfirmModal([
    'id' => 'removePrevalentPartnershipPopup',
    'modalDescriptionText' => AmosAdmin::t('amosadmin', '#remove_prevalent_partnerhip_confirm'),
    'confirmBtnLink' => Yii::$app->urlManager->createUrl([
        '/admin/user-profile/remove-prevalent-partnership',
        'id' => $model->id
    ]),
    'confirmBtnOptions' => ['id' => 'confirm-remove-pp-btn', 'class' => 'btn btn-primary']
]);

$js = <<<JS

$('#confirm-remove-pp-btn').on("click", function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('href'),
        type : "POST"
    });
    return false;
});

JS;
$this->registerJs($js);

?>

<?php if ($model->isNewRecord): ?>
    <section class="section-data">
        <div class="row">
            <div class="col-xs-12">
                <?= Alert::widget([
                    'type' => Alert::TYPE_WARNING,
                    'body' => AmosAdmin::t('amosadmin', 'Before choose prevalent partnership click on the CREATE button in the bottom to save the profile.'),
                    'closeButton' => false
                ]); ?>
            </div>
        </div>
    </section>
<?php else: ?>
    <section class="section-data">
        <div class="row">
            <h4><?= AmosAdmin::t('amosadmin', 'Choose the organization with which you mainly work with those already on the platform. If the organization is not present, you may be required to register later.') ?></h4>
        </div>
    </section>
    <section class="section-data">
        <div class="row">
            <div id="prevalent-partnership-section">
                <?php if (!is_null($model->prevalentPartnership)): ?>
                    <div class="col-xs-3">
                        <div class="img-profile">
                            <?php
                            $admin =  AmosAdmin::getInstance();
                            /** @var  $organizationsModule OrganizationsModuleInterface*/
                            $organizationsModule = \Yii::$app->getModule($admin->getOrganizationModuleName());
                            $widgetClass = $organizationsModule->getOrganizationCardWidgetClass();
                            echo $widgetClass::widget(['model' => $model->prevalentPartnership]);
                            ?>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div><?= $model->prevalentPartnership->getTitle() ?></div>
                        <div><?= Html::a(AmosAdmin::t('amosadmin', 'Change prevalent partnership'), ['/admin/user-profile/associate-prevalent-partnership', 'id' => $model->id, 'viewM2MWidgetGenericSearch' => true]) ?></div>
                        <div>
                            <?= Html::a(AmosAdmin::t('amosadmin', 'Remove prevalent partnership'),  ['/admin/user-profile/remove-prevalent-partnership', 'id' => $model->id], [ 'data-toggle' => 'modal', 'data-target' => '#removePrevalentPartnershipPopup' ]) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-xs-7">
                        <div><?= AmosAdmin::tHtml('amosadmin', 'Prevalent partnership not selected') ?></div>
                        <div><?= Html::a(AmosAdmin::t('amosadmin', 'Select prevalent partnership'), ['/admin/user-profile/associate-prevalent-partnership', 'id' => $model->id, 'viewM2MWidgetGenericSearch' => true]) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?= $form->field($model, 'prevalent_partnership_id')->hiddenInput()->label(false) ?>
    </section>
<?php endif; ?>
