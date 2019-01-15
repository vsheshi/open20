<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\widgets
 * @category   CategoryName
 */

namespace lispa\amos\admin\widgets;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserContact;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\user\User;
use lispa\amos\core\utilities\JsUtility;
use Yii;
use yii\base\Widget;
use yii\web\View;
use yii\widgets\PjaxAsset;

/**
 * Class UserContacsWidget
 * @package lispa\amos\admin\widgets
 */
class UserContacsWidget extends Widget
{

    /**
     * @var int $userId
     */
    public $userId = null;

    /**
     * @var bool|false true if we are in edit mode, false if in view mode or otherwise
     */
    public $isUpdate = false;

    /**
     * @var string $gridId
     */
    public $gridId = 'user-contanct-grid';

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (is_null($this->userId)) {
            throw new \Exception(AmosAdmin::t('amosadmin', 'Missing user id'));
        }

    }

    /**
     * @return mixed
     */
    public function run()
    {

        $gridId = $this->gridId;
        $url = \Yii::$app->urlManager->createUrl([
            '/admin/user-profile/contacts',
            'id' => $this->userId,
            'isUpdate' => $this->isUpdate
            ]);
        $searchPostName = 'searchContactsName';

        $js = JsUtility::getSearchM2mFirstGridJs($gridId, $url, $searchPostName);
        PjaxAsset::register($this->getView());
        $this->getView()->registerJs($js, View::POS_LOAD);

        $itemsMittente = [
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

                    /** @var UserContact $model */
                    if($this->userId == $model->user_id) {
                        $profile = User::findOne($model->contact_id)->getProfile();
                    }else{
                        $profile = User::findOne($model->user_id)->getProfile();
                    }
                    return UserCardWidget::widget(['model' => $profile]);
                }
            ],
            'name' => [
                'headerOptions' => [
                    'id' => AmosAdmin::t('amosadmin', 'Name'),
                ],
                'contentOptions' => [
                    'headers' => AmosAdmin::t('amosadmin', 'Name'),
                ],
                'label' => AmosAdmin::t('amosadmin', 'Name'),
                'value' => function ($model) {
                    /** @var UserContact $model */
                    if($this->userId == $model->user_id) {
                        $userProfile = User::findOne($model->contact_id)->getProfile();
                        $name = $userProfile->getNomeCognome();
                    }else{
                        $userProfile = User::findOne($model->user_id)->getProfile();
                        $name = $userProfile->getNomeCognome();
                    }
                    return Html::a($name, ['/admin/user-profile/view', 'id' => $userProfile->id ], [
                        'title' => AmosAdmin::t('amoscommunity', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $name])
                    ]);
                },

                'format' => 'html'
            ],
            'status' => [
                'attribute' => 'status',
                'label' => AmosAdmin::t('amosadmin', 'Status'),
                'headerOptions' => [
                    'id' => AmosAdmin::t('amosadmin', 'Status'),
                ],
                'contentOptions' => [
                    'headers' => AmosAdmin::t('amosadmin', 'Status'),
                ],
                'value' => function($model){
                    /** @var UserContact $model */
                    if($model->status == UserContact::STATUS_INVITED) {
                        return AmosAdmin::t('amosadmin', 'Waiting for acceptance');
                    } else {
                        return AmosAdmin::t('amosadmin', 'Connected');
                    }
                }
            ],
            'created_at' => [
                'attribute' => 'created_at',
                'format' => 'dateTime',
            ],
            'accepted_at' => [
                'attribute' => 'accepted_at',
                'format' => 'dateTime',
            ],
        ];

//        UserContact::find()-> andWhere("user_id = ".$this->userId. " OR contact_id = ".$this->userId)->andWhere(['<>', 'status', UserContact::STATUS_REFUSED]);

        $contactsInvited = UserContact::find()
            ->innerJoin('user_profile', 'user_profile.user_id = user_contact.contact_id')
            ->andWhere('user_contact.deleted_at IS NULL AND user_profile.deleted_at IS NULL')
            ->andWhere("user_contact.user_id = ".$this->userId)->andWhere(['<>', 'user_contact.status', UserContact::STATUS_REFUSED])
            ->andWhere(['user_profile.attivo' => 1]);

        $contactsInviting= UserContact::find()->innerJoin('user_profile', 'user_profile.user_id = user_contact.user_id')
            ->andWhere('user_contact.deleted_at IS NULL AND user_profile.deleted_at IS NULL')
            ->andWhere("user_contact.contact_id = ".$this->userId)->andWhere(['<>', 'user_contact.status', UserContact::STATUS_REFUSED])
            ->andWhere(['user_profile.attivo' => 1]);

        if(isset($_POST[$searchPostName])){
            $searchName = $_POST[$searchPostName];
            if(!empty($searchName)){
                $contactsInvited->andWhere("nome like '%" . $searchName . "%' OR cognome like '%" . $searchName . "%' ");
                $contactsInviting->andWhere("nome like '%" . $searchName . "%' OR cognome like '%" . $searchName . "%' ");
            }
        }
        $contacts = UserContact::find()
            ->select('*')
            ->from([UserContact::tableName() => $contactsInvited->union($contactsInviting)]);

        $model = User::findOne($this->userId)->getProfile();
        $loggedUserId = Yii::$app->getUser()->id;
        $this->isUpdate = $this->isUpdate && ($loggedUserId == $model->user_id);

        $widget = \lispa\amos\core\forms\editors\m2mWidget\M2MWidget::widget([
            'model' => $model,
            'modelId' => $model->user_id,
            'modelData' => $contacts,
            'overrideModelDataArr' => true,
            'forceListRender' => true,
            'targetUrlParams' => [
                'viewM2MWidgetGenericSearch' => true
            ],
            'gridId' => $gridId,
            'firstGridSearch' => true,
            'itemsSenderPageSize' => 10,
            'pageParam' => 'page-contacts',
            'disableCreateButton' => true,
            'createAssociaButtonsEnabled' => $this->isUpdate,
            'btnAssociaLabel' => AmosAdmin::t('amosadmin', 'Add new contacts'),
            'actionColumnsTemplate' => $this->isUpdate ? '{connect}{deleteRelation}' : '',
            'targetUrl' => '/admin/user-contact/associate-contacts',
            'createNewTargetUrl' => '/admin/user-profile/create',
            'moduleClassName' => AmosAdmin::className(),
            'targetUrlController' => 'user-contact',
            'postName' => 'UserContact',
            'postKey' => 'userContact',
            'permissions' => [
                'add' => 'USERPROFILE_UPDATE',
                'manageAttributes' => 'USERPROFILE_UPDATE'
            ],
            'actionColumnsButtons' => [
                'connect' => function ($url, $model) {
                    $btn = ConnectToUserWidget::widget(['model' => $model, 'isGridView' => true ]);
                    return $btn;
                },

                'deleteRelation' => function ($url, $model) {
                    $url = '/admin/user-contact/delete-contact';
                    $urlDelete = Yii::$app->urlManager->createUrl([
                        $url,
                        'id' => $model->id,
                    ]);
                    $loggedUserId = Yii::$app->getUser()->id;
                    if ($loggedUserId == $this->userId /*&& ($model->created_by != $loggedUser->id || $loggedUser->can('ADMIN'))*/) {
                        if($model->user_id == $loggedUserId) {
                            $name = User::findOne($model->contact_id)->getProfile()->getNomeCognome();
                        } else {
                            $name = User::findOne($model->user_id)->getProfile()->getNomeCognome();
                        }
                        $btnDelete = Html::a( AmosIcons::show('close', ['class' => 'btn-delete-relation']),
//                            '<p class="btn btn-tool-secondary">' . AmosIcons::show('close') . '</p>'
                            $urlDelete,
                            [
                                'title' => AmosAdmin::t('amosadmin', 'Delete'),
                                'data-confirm' => AmosAdmin::t('amosadmin', 'Are you sure to remove'). " " . $name ." "
                                    . AmosAdmin::t('amosadmin', 'from your contact list'),
                            ]
                        );
                    } else {
                        $btnDelete = '';
                    }
                    return $btnDelete;
                }
            ],
            'itemsMittente' => $itemsMittente,
        ]);

        echo '';
        return "<div id=\"user-contanct-grid\" data-pjax-container=\"user-contanct-grid-pjax\" data-pjax-timeout=\"1000\">
                <h3>".AmosAdmin::tHtml('amosadmin', 'Contacts')."</h3>"
                .$widget
                ."</div>";
    }
}