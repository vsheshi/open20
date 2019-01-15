<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\views\my-activities
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\admin\widgets\UserCardWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\myactivities\AmosMyActivities;


/** @var $model \lispa\amos\myactivities\basic\OrganizationsToValidate */

?>
<div class="wrap-activity">
    <div class="col-md-1 col-xs-2 icon-plugin">
        <?= AmosIcons::show('building', [], 'dash') ?>
    </div>
    <?= \lispa\amos\myactivities\widgets\UserRequestValidation::widget([
        'model' => $model,
        'labelKey' => 'Validation organizations'
    ]) ?>
    <div class="col-md-3 col-xs-12 wrap-action">
        <?php
        if ($model->status == $model::ORGANIZATIONS_WORKFLOW_STATUS_TO_VALIDATE) {
            echo Html::a(AmosIcons::show('check') . ' ' . AmosMyActivities::t('amosmyactivities', 'Take charge'),
                Yii::$app->urlManager->createUrl([
                    '/organizations/organizations/in-validation-organization',
                    'id' => $model->id,
                ]),
                ['class' => 'btn btn-secondary']
            );
        } else {
            echo Html::a(AmosIcons::show('check') . ' ' . AmosMyActivities::t('amosmyactivities', 'Validate'),
                Yii::$app->urlManager->createUrl([
                    '/organizations/organizations/validate-organization',
                    'id' => $model->id,
                ]),
                ['class' => 'btn btn-primary']
            );

            echo Html::a(AmosIcons::show('close') . ' ' . AmosMyActivities::t('amosmyactivities', 'Reject'),
                Yii::$app->urlManager->createUrl([
                    '/organizations/organizations/reject-organization',
                    'id' => $model->id,
                ]),
                ['class' => 'btn btn-secondary']
            );
        }
        ?>
    </div>
</div>
<hr>



