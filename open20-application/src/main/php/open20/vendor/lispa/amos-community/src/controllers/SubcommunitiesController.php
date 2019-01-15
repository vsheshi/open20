<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\controllers
 * @category   CategoryName
 */

namespace lispa\amos\community\controllers;

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\search\CommunitySearch;
use Yii;
use yii\helpers\Url;

/**
 * Class SubcommunitiesController
 * @package lispa\amos\community\controllers
 */
class SubcommunitiesController extends CommunityController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setModelSearch(new CommunitySearch(['subcommunityMode' => true]));
    }
    
    /**
     * Base operations in order to render different list views
     * @return string
     */
    protected function baseListsAction($pageTitle, $layout = null)
    {
        Url::remember();
        $parentId = null;
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $parentId = $scope['community'];
            }
        }
        $urlCreation = ['/community/community-wizard/introduction', 'parentId' => $parentId];
        if (!Yii::$app->getModule('community')->enableWizard) {
            $urlCreation = ['/community/community/create', 'parentId' => $parentId];
        }
        Yii::$app->view->params['createNewBtnParams'] = [
            'createNewBtnLabel' => AmosCommunity::tHtml('amoscommunity', '#btn_new_subcommunity'),
            'urlCreateNew' => $urlCreation
        ];
		
		Yii::$app->view->params['subtitle'] = AmosCommunity::t("amoscommunity", "#msg_header_index_WorkingSubAreas",['user' => \Yii::$app->getUser()->getIdentity()->getProfile()->getNomeCognome()]);
		
        Yii::$app->session->set(AmosCommunity::beginCreateNewSessionKey(), Url::previous());
        $this->layout = "@vendor/lispa/amos-core/views/layouts/list";
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();
        $this->setListsBreadcrumbs($pageTitle);
        return $this->render('@vendor/lispa/amos-community/src/views/community/index', [
            'dataProvider' => $this->getDataProvider(),
            'model' => $this->getModelSearch(),
            'currentView' => $this->getCurrentView(),
            'availableViews' => $this->getAvailableViews(),
            'url' => ($this->url) ? $this->url : null,
            'parametro' => ($this->parametro) ? $this->parametro : null
        ]);
    }
    
    /**
     * @inheritdoc
     */
    protected function setListsBreadcrumbs($pageTitle)
    {
        $translatedTitle = AmosCommunity::t('amoscommunity', $pageTitle);
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh) && !empty($moduleCwh->getCwhScope())) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                $parentId = $scope['community'];
                $communityParent = Community::findOne($parentId);
                if (isset($communityParent)) {
                    $translatedTitle = AmosCommunity::t('amoscommunity', '#subcommunities_in') . ' ';
                }
            }
        }
        Yii::$app->view->title = $translatedTitle;
        Yii::$app->view->params['breadcrumbs'][] = $translatedTitle;

    }
}
