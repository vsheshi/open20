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

/**
 * @var yii\web\View $this
 * @var lispa\amos\core\forms\ActiveForm $form
 * @var lispa\amos\admin\models\UserProfile $model
 * @var lispa\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

?>

<?php if ($adminModule->confManager->isVisibleField('userProfileImage', ConfigurationManager::VIEW_TYPE_FORM)): ?>
    <?= $form->field($model, 'userProfileImage')->widget(\lispa\amos\attachments\components\AttachmentsInput::classname(), [
        'options' => [
            'multiple' => false
        ],
        'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
            'maxFileCount' => 1,
            'showRemove' => false,// Client max files,
            'indicatorNew' => false,
            'allowedPreviewTypes' => ['image'],
            'previewFileIconSettings' => false,
            'overwriteInitial' => false,
            'layoutTemplates' => false
        ]
    ])->label(AmosAdmin::t('amosadmin', 'Immagine del profilo')) ?>
<?php endif; ?>
