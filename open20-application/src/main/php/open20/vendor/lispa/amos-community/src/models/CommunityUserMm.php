<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\models;
use lispa\amos\community\AmosCommunity;
use yii\db\ActiveQuery;

/**
 * Class CommunityUserMm
 * This is the model class for table "community_user_mm".
 * @package backend\modules\corsi\models
 */
class CommunityUserMm extends \lispa\amos\community\models\base\CommunityUserMm
{

    /**
     * Gets the list of community manager email address for a specific community
     *
     * @param integer $communityId The Id of community to search community/user relations
     * @return array $emailArray The array containing community manager email
     */
    public function getCommunityManagerMailList($communityId){

        /** @var ActiveQuery $queryManagers */
        $community = Community::findOne($communityId);
        $callingModule = \Yii::createObject($community->context);
        $managerRole = $callingModule->getManagerRole();
        $queryManagers = CommunityUserMm::find()->andWhere([
            'community_id' => $communityId,
            'role' => $managerRole
        ]);
        $queryManagers->innerJoin('user', 'user_id = user.id');
        $queryManagers->select('user.email');
        $emailArray = $queryManagers->column();
        return $emailArray;
    }

    /**
     * @return string
     */
    public function getPersonalizedStatus(){
        if($this->status == CommunityUserMm::STATUS_ACTIVE && $this->access_to_community == 1) {
            return AmosCommunity::t('amoscommunity', $this->status);
        } else {
            return AmosCommunity::t('amoscommunity', CommunityUserMm::STATUS_WAITING_OK_USER);
        }
    }

}
