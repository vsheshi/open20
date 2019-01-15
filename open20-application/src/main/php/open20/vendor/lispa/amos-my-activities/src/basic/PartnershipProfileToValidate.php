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
use lispa\amos\partnershipprofiles\models\search\PartnershipProfilesSearch;
use lispa\amos\partnershipprofiles\models\PartnershipProfiles;

/**
 * Class CollaborationProposalToValidate
 * @package lispa\amos\myactivities\basic
 */
class PartnershipProfileToValidate extends PartnershipProfilesSearch implements MyActivitiesModelsInterface
{

    public function getSearchString()
    {
        return $this->title;
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
     * @return PartnershipProfiles
     */
    public function getWrappedObject()
    {
        return PartnershipProfiles::findOne($this->id);
    }


}