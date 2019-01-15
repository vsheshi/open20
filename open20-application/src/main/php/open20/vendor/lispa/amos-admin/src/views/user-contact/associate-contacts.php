<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-contact
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\helpers\Html;

/**
 * @var \lispa\amos\admin\models\UserProfile $model
 */


$this->title = AmosAdmin::t('amosadmin', 'Add contacts');
$this->params['breadcrumbs'][] = AmosAdmin::t('amosadmin', 'Add contacts');

$userId = Yii::$app->request->get("id");

/**
 * @var \yii\db\ActiveQuery $query UserProfiles to invite or with pending invitation
 */
$query = $model->getUserNetworkAssociationQuery();

$query->orderBy([
        'user_profile.cognome' => SORT_ASC,
        'user_profile.nome' => SORT_ASC,
    ]);

$post = Yii::$app->request->post();
if (isset($post['genericSearch'])) {
    $query->andFilterWhere(['OR',['like', UserProfile::tableName() . '.cognome', $post['genericSearch']],
        ['like', UserProfile::tableName() . '.nome', $post['genericSearch']]]);
}

?>
<?= \lispa\amos\core\forms\editors\m2mWidget\M2MWidget::widget([
    'model' => $model,
    'modelId' => $model->id,
    'modelData' => $query,
    'modelDataArrFromTo' => [
        'from' => 'id',
        'to' => 'id'
    ],
    'modelTargetSearch' => [
        'class' => \lispa\amos\admin\models\UserProfile::className(),
        'query' => $query,
    ],
    'targetFooterButtons' => Html::a(AmosAdmin::t('amosadmin', 'Close'), Yii::$app->urlManager->createUrl([
        '/admin/user-contact/annulla-m2m',
        'id' => $userId
    ]), ['class' => 'btn btn-secondary', 'AmosAdmin' => Yii::t('amosadmin', 'Close')]),
    'renderTargetCheckbox' => false,
    'viewSearch' => (isset($viewM2MWidgetGenericSearch) ? $viewM2MWidgetGenericSearch : false),
    'targetUrlController' => 'user-contact',
    'targetActionColumnsTemplate' => '{connect}',
    'moduleClassName' => \lispa\amos\admin\AmosAdmin::className(),
    'postName' => 'UserContact',
    'postKey' => 'user-contact',
    'targetColumnsToView' => [
        'photo' => [
            'headerOptions' => [
                'id' => AmosAdmin::t('amosadmin', 'Photo'),
            ],
            'contentOptions' => [
                'headers' => AmosAdmin::t('amosadmin', 'Photo'),
            ],
            'label' => AmosAdmin::t('amosadmin', 'Photo'),
            'format' => 'raw',
            'value' => function ($model) {
                return \lispa\amos\admin\widgets\UserCardWidget::widget(['model' => $model, 'onlyAvatar'=> true]);
            }
        ],
        'name' => [
            'attribute' => 'surnameName',
            'headerOptions' => [
                'id' => AmosAdmin::t('amosadmin', 'Name'),
            ],
            'contentOptions' => [
                'headers' => AmosAdmin::t('amosadmin', 'Name'),
            ],
            'label' => AmosAdmin::t('amosadmin', 'Name'),
            'value' => function($model){
                return Html::a($model->surnameName, ['/admin/user-profile/view', 'id' => $model->id ], [
                    'title' => AmosAdmin::t('amosnews', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $model->surnameName])
                ]);
            },
            'format' => 'html'
        ],
        [
            'class' => 'lispa\amos\core\views\grid\ActionColumn',
            'template' => '{connect}',
            'buttons' => [
                'connect' =>  function ($url, $model) {
                    return \lispa\amos\admin\widgets\ConnectToUserWidget::widget([ 'model' => $model, 'isGridView' => true ]);
                }
            ]
        ]
    ],
]);
?>