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
$query = UserProfile::find();
$query
    ->andWhere(['user_id' => $facilitatorUserIds])
    ->orderBy(['cognome' => SORT_ASC, 'nome' => SORT_ASC]);
$post = Yii::$app->request->post();

if (isset($post['genericSearch'])) {
    $query->andFilterWhere(['or',
        ['like', UserProfile::tableName() . '.cognome', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.nome', $post['genericSearch']],
        ['like', "CONCAT( ". UserProfile::tableName() .".nome , ' ', ". UserProfile::tableName() .".cognome )", $post['genericSearch']],
        ['like', "CONCAT( ". UserProfile::tableName() .".cognome , ' ', ". UserProfile::tableName() .".nome )", $post['genericSearch']],
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
$js = <<<JS
var hiddenInputContainer = $('.hiddenInputContainer');
$('body').on('click', '.confirmBtn', function(event) {
    event.preventDefault();
   var selectedId = $(this).data('model_id');
   var newHiddenInput = '<input type="hidden" name="$formName'+'[$postKey][]" value="'+ selectedId + '"/>';
   var selection = '<input type="hidden" name="selection[]" value="'+ selectedId + '"/>';
   hiddenInputContainer.empty();
   hiddenInputContainer.append(newHiddenInput);
   hiddenInputContainer.append(selection);
   hiddenInputContainer.parents('form').submit();
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>


<?= M2MWidget::widget([
    'model' => $model,
    'modelId' => $model->id,
    'modelData' => UserProfile::find()->andWhere(['id' => $model->facilitatore_id]),
    'modelDataArrFromTo' => [
        'from' => 'facilitatore_id',
        'to' => 'user_id'
    ],
    'modelTargetSearch' => [
        'class' => UserProfile::className(),
        'query' => $query,
    ],
    'gridId' => 'associate-facilitator',
    'viewSearch' => (isset($viewM2MWidgetGenericSearch) ? $viewM2MWidgetGenericSearch : false),
    'multipleSelection' => false,
    'relationAttributesArray' => ['status', 'role'],
    'moduleClassName' => AmosAdmin::className(),
    'postName' => $formName,
    'postKey' => $postKey,
    'listView' => '@vendor/lispa/amos-admin/src/views/user-profile/_item',
    'targetFooterButtons' => M2MWidget::makeCancelButton(AmosAdmin::className(), 'user-profile', $model),
    'targetUrlController' => 'user-profile',
    'targetUrlParams' => [
        'viewM2MWidgetGenericSearch' => true
    ],
    'targetColumnsToView' => [
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

