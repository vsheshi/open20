<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\basic
 * @category   CategoryName
 */
namespace lispa\amos\myactivities\basic;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\discussioni\models\DiscussioniTopic;

/**
 * Class DiscussionToValidate
 * @package lispa\amos\myactivities\basic
 */
class DiscussionToValidate extends \lispa\amos\discussioni\models\search\DiscussioniTopicSearch implements MyActivitiesModelsInterface
{

    public function getSearchString()
    {
        return $this->titolo;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @return string
     */
    public function getCreatorNameSurname()
    {
        /** @var UserProfile $userProfile */
        $userProfile = UserProfile::find()->andWhere(['user_id' => $this->created_by])->one();
        if (!empty($userProfile)){
            return $userProfile->getNomeCognome();
        }
        return '';
    }

    /**
     * @return DiscussioniTopic
     */
    public function getWrappedObject()
    {
        return DiscussioniTopic::findOne($this->id);
    }


}