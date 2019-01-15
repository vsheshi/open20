<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\rules
 * @category   CategoryName
 */

namespace lispa\amos\community\rules;

use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityType;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\core\rules\DefaultOwnContentRule;

/**
 * Class DeleteOwnCommunitiesRule
 * @package lispa\amos\community\rules
 */
class DeleteOwnCommunitiesRule extends DefaultOwnContentRule
{
    public $name = 'deleteOwnCommunities';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if (isset($params['model'])) {
            /** @var Record $model */
            $model = $params['model'];
            if (!$model->id) {
                $post = \Yii::$app->getRequest()->post();
                $get = \Yii::$app->getRequest()->get();
                if (isset($get['id'])) {
                    $model = $this->instanceModel($model, $get['id']);
                } elseif (isset($post['id'])) {
                    $model = $this->instanceModel($model, $post['id']);
                }
            }
            return (
                ($model->isCommunityManager() && $model->parent_id) && // Se l'utente e' community manager della stanza corrente
                $this->canReadCommunity($model, $user) // Se l'utente puo' visualizzare la community
            );
        } else {
            return false;
        }
    }

    private function canReadCommunity($model, $user) {
        /** @var Community $model */
        if($model->community_type_id == CommunityType::COMMUNITY_TYPE_OPEN || $model->community_type_id == CommunityType::COMMUNITY_TYPE_PRIVATE){
            return true;
        } elseif($model->community_type_id == CommunityType::COMMUNITY_TYPE_CLOSED){
            $communityUserMm = CommunityUserMm::find()->andWhere(['community_id' => $model->id])->andWhere(['user_id' => $user])->one();
            if(!empty($communityUserMm)) {
                return true;
            }
        }
        return false;
    }

}
