<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\widgets;

use lispa\amos\organizzazioni\models\Profilo;
use lispa\amos\organizzazioni\models\ProfiloUserMm;
use lispa\amos\organizzazioni\Module;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\user\User;
use lispa\amos\core\utilities\JsUtility;
use Yii;
use yii\base\Widget;
use yii\web\View;
use yii\widgets\PjaxAsset;

/**
 * Class UserNetworkWidget
 * @package openinnovation\organizations\widgets
 */
class UserNetworkWidget extends Widget
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
    public $gridId = 'user-organizzazioni-grid';

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (is_null($this->userId)) {
            throw new \Exception(Module::t('amosorganizzazioni', '#Missing_user_id'));
        }

    }


    /**
     * @return mixed
     */
    public function run()
    {
        $gridId = $this->gridId;
        $url = \Yii::$app->urlManager->createUrl([
            '/organizzazioni/profilo/user-network',
            'userId' => $this->userId,
            'isUpdate' => $this->isUpdate
        ]);
        $searchPostName = 'searchProfiloName';

        $js = JsUtility::getSearchM2mFirstGridJs($gridId, $url, $searchPostName);
        PjaxAsset::register($this->getView());
        $this->getView()->registerJs($js, View::POS_LOAD);

         $itemsMittente = [
             'logo_id' => [
                 'headerOptions' => [
                     'id' => Module::t('amosorganizzazioni', '#logo'),
                 ],
                 'contentOptions' => [
                     'headers' => Module::t('amosorganizzazioni', '#logo'),
                 ],
                 'label' => Module::t('amosorganizzazioni', '#logo'),
                 'format' => 'raw',
                 'value' => function ($model) {
                        return ProfiloCardWidget::widget(['model' => $model]);
                 }
             ],
             'name',
             'created_by' => [
                 'attribute' => 'created_by',
                 'format' => 'html',
                 'value' => function($model){
                     /** @var Profilo $model */
                     $name = '-';
                     if(!is_null($model->created_by)) {
                         $creator = User::findOne($model->created_by);
                         if(!empty($creator)) {
                             return $creator->getProfile()->getNomeCognome();
                         }
                     }
                     return $name;
                 }
             ],
         ];

        $organizations = Profilo::find()->innerJoin(ProfiloUserMm::tableName(),
            ProfiloUserMm::tableName() . '.user_id =' . $this->userId
            . " AND " . ProfiloUserMm::tableName() . '.profilo_id = '. Profilo::tableName() .'.id')
            ->andWhere(ProfiloUserMm::tableName() . '.deleted_at IS NULL')
            ->andWhere(Profilo::tableName() .'.deleted_at IS NULL');


        if(isset($_POST[$searchPostName])){
            $searchName = $_POST[$searchPostName];
            if(!empty($searchName)){
                $organizations->andWhere(Profilo::tableName() .".name like '%" . $searchName . "%'");
            }
        }

        $model = User::findOne($this->userId)->getProfile();
        $loggedUserId = Yii::$app->getUser()->id;
        $this->isUpdate = $this->isUpdate && ($loggedUserId == $model->user_id);

        $widget = \lispa\amos\core\forms\editors\m2mWidget\M2MWidget::widget([
            'model' => $model,
            'modelId' => $model->id,
            'modelData' => $organizations,
            'overrideModelDataArr' => true,
            'forceListRender' => true,
            'targetUrlParams' => [
                'viewM2MWidgetGenericSearch' => true
            ],
            'gridId' => $gridId,
            'firstGridSearch' => true,
            'itemsSenderPageSize' => 10,
            'pageParam' => 'page-organizations',
            'disableCreateButton' => true,
            'createAssociaButtonsEnabled' => $this->isUpdate,
            'btnAssociaLabel' => Module::t('amosorganizzazioni', '#add_new_organization'),
            'actionColumnsTemplate' => $this->isUpdate ? '{joinOrganization}{deleteRelation}' : '',
            'deleteRelationTargetIdField' => 'user_id',
            'targetUrl' => '/organizzazioni/profilo/associate-organization-m2m',
            'createNewTargetUrl' => '/admin/user-profile/create',
            'moduleClassName' => Module::className(),
            'targetUrlController' => 'organizzazioni',
            'postName' => 'User',
            'postKey' => 'user',
            'permissions' => [
                'add' => 'USERPROFILE_UPDATE',
                'manageAttributes' => 'USERPROFILE_UPDATE'
            ],
            'actionColumnsButtons' => [
                'deleteRelation' => function ($url, $model) {
                    $url = '/organizzazioni/profilo/elimina-m2m';
                    $organizationId = $model->id;
                    $targetId = $this->userId;
                    $urlDelete = Yii::$app->urlManager->createUrl([
                        $url,
                        'id' => $organizationId,
                        'targetId' => $targetId
                    ]);
                    $loggedUser = Yii::$app->getUser();
                    if ($loggedUser->id == $this->userId && ($model->created_by != $loggedUser->id || $loggedUser->can('ADMIN'))) {
                        $btnDelete = Html::a( AmosIcons::show('close', ['class' => 'btn-delete-relation']),
//                            '<p class="btn btn-tool-secondary">' . AmosIcons::show('close') . '</p>',
                            $urlDelete,
                            [
                                'title' => Module::t('amosorganizzazioni', '#delete'),
                                'data-confirm' => Module::t('amosorganizzazioni', '#are_you_sure_cancel'),
                            ]
                        );
                    } else {
                        $btnDelete = '';
                    }
                    return $btnDelete;
                },
            ],
            'itemsMittente' => $itemsMittente,
        ]);

        return "<div id='".$gridId."' data-pjax-container='".$gridId."-pjax' data-pjax-timeout=\"1000\">"
                ."<h3>".Module::tHtml('amosorganizzazioni', '#organization')."</h3>"
                .$widget."</div>";
    }


}