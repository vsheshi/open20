<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\widgets\icons;

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\search\CommunitySearch;
use lispa\amos\core\widget\WidgetIcon;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconCommunity
 * @package lispa\amos\community\widgets\icons
 */
class WidgetIconCommunity extends WidgetIcon
{
    public function init()
    {
        parent::init();

        $this->setLabel(AmosCommunity::tHtml('amoscommunity', 'All communities'));
        $this->setDescription(AmosCommunity::t('amoscommunity', "Allow user to edit the community entity"));

        $this->setIcon('group');
        $url = '/community/community/index';
        $moduleCwh = \Yii::$app->getModule('cwh');
        if(isset($moduleCwh) && !empty($moduleCwh->getCwhScope())){
            $scope = $moduleCwh->getCwhScope();
            if(isset($scope['community'])){
                $url = '/community/subcommunities/index?id='. $scope['community'];
            }
        }
        $this->setUrl([$url]);
        $this->setCode('COMMUNITY');
        $this->setModuleName('community');
        $this->setNamespace(__CLASS__);

        /*$communitySearch = new CommunitySearch();
        $notifier = \Yii::$app->getModule('notify');
        $count = 0;
        if($notifier)
        {
            $count = $notifier->countNotRead(Yii::$app->getUser()->id, Community::class, $communitySearch->buildQuery('all',[]));
        }
        $this->setBulletCount($count);
        */
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-lightPrimary'
        ]));
    }

    public static function widgetLabel()
    {
        return AmosCommunity::t('amoscommunity', 'All communities');
    }
}
