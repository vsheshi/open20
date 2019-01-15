<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\first-access-wizard
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\admin\utility\UserProfileUtility;
use lispa\amos\core\forms\editors\m2mWidget\M2MWidget;
use lispa\amos\core\user\User;
use yii\db\ActiveQuery;
use yii\web\View;

/**
 * @var \yii\web\View $this
 * @var \lispa\amos\admin\models\UserProfile $model
 */

$this->title = AmosAdmin::t('amosadmin', 'Select facilitator for') . ' ' . $model->getNomeCognome();

// All facilitators without the user profile in modify.
$toSkipFacilitatorIds = [$model->user_id];
if (!is_null($model->facilitatore)) {
    $toSkipFacilitatorIds[] = $model->facilitatore->user_id;
}
$facilitatorUserIds = array_diff(UserProfileUtility::getAllFacilitatorUserIds(), $toSkipFacilitatorIds);

/** @var ActiveQuery $query */
$query = User::find();
$query->innerJoinWith('userProfile')
    ->andWhere(['in', User::tableName() . '.id', $facilitatorUserIds])
    ->orderBy(['cognome' => SORT_ASC, 'nome' => SORT_ASC]);
$post = Yii::$app->request->post();

if (isset($post['genericSearch'])) {
    $query->andFilterWhere(['or',
        ['like', UserProfile::tableName() . '.cognome', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.nome', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.codice_fiscale', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.domicilio_indirizzo', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.indirizzo_residenza', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.domicilio_localita', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.domicilio_cap', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.cap_residenza', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.numero_civico_residenza', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.domicilio_civico', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.telefono', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.cellulare', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.email_pec', $post['genericSearch']],
    ]);
}

$formName = 'UserProfile';
$postKey = 'user';
$js = "
var hiddenInputContainer = $('.hiddenInputContainer');
$('.set-facilitator-btn').on('click', function(event) {
    event.preventDefault();
    $(this).data('model_id');" . '
    var newHiddenInput = \'<input type="hidden" name="' . $formName . '[' . $postKey . '][]" value="\' + $(this).data(\'model_id\') + \'"/>\';
    var selection = \'<input type="hidden" name="selection[]" value="\' + $(this).data(\'model_id\') + \'"/>\';' . "
    hiddenInputContainer.empty();
    hiddenInputContainer.append(newHiddenInput);
    hiddenInputContainer.append(selection);
    var confirmText = '" . AmosAdmin::t('amosadmin', 'You have selected') . " ' + $(this).data('model_name') + ' ' + $(this).data('model_surname') + \" " . AmosAdmin::t('amosadmin', 'as your facilitator. To confirm click on the CONFIRM button. At the confirm the facilitator will be bound to the user profile.') . "\";
    if (confirm(confirmText)) {
        hiddenInputContainer.parents('form').submit();
    }
});
";
$this->registerJs($js, View::POS_READY);

?>

<?= M2MWidget::widget([
    'model' => $model,
    'modelId' => $model->id,
    'modelData' => UserProfile::find()->andWhere(['id' => $model->facilitatore_id]),
    'modelDataArrFromTo' => [
        'from' => 'id',
        'to' => 'id'
    ],
    'modelTargetSearch' => [
        'class' => User::className(),
        'query' => $query,
    ],
    'viewSearch' => (isset($viewM2MWidgetGenericSearch) ? $viewM2MWidgetGenericSearch : false),
    'multipleSelection' => false,
    'relationAttributesArray' => ['status', 'role'],
    'moduleClassName' => AmosAdmin::className(),
    'postName' => $formName,
    'postKey' => $postKey,
    'itemsTargetView' => '@vendor/lispa/amos-admin/src/views/user-profile/_item',
    'targetFooterButtons' => M2MWidget::makeCancelButton(AmosAdmin::className(), 'first-access-wizard', $model),
    'targetUrlController' => 'first-access-wizard',
    'targetUrlParams' => [
        'viewM2MWidgetGenericSearch' => true
    ],
    'targetColumnsToView' => [
//        'User image' => [
//            'headerOptions' => [
//                'id' => AmosAdmin::t('amosadmin', 'User image'),
//            ],
//            'contentOptions' => [
//                'headers' => AmosAdmin::t('amosadmin', 'User image'),
//            ],
//            'label' => AmosAdmin::t('amosadmin', 'User image'),
//            'format' => 'html',
//            'value' => function ($model) {
//                /** @var \lispa\amos\core\user\User $model */
//                /** @var \lispa\amos\admin\models\UserProfile $userProfile */
//                $userProfile = $model->getProfile();
//
//                $url = $userProfile->getAvatarUrl('original');
//
//                return \lispa\amos\core\helpers\Html::img($url, [
//                    'class' => 'gridview-image',
//                    'alt' => AmosAdmin::t('amosadmin', 'User image')
//                ]);
//            }
//        ],
        'name' => [
            'attribute' => 'profile.surnameName',
            'label' => AmosAdmin::t('amosadmin', 'Name'),
            'headerOptions' => [
                'id' => AmosAdmin::t('amosadmin', 'Name'),
            ],
            'contentOptions' => [
                'headers' => AmosAdmin::t('amosadmin', 'Name'),
            ]
        ],
    ],
]);
?>
