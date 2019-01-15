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

/**
 *
 * Class ReportToRead
 * @package lispa\amos\myactivities\basic
 */
class ReportToRead extends \lispa\amos\report\models\Report implements MyActivitiesModelsInterface
{

    public function getSearchString()
    {
        return $this->content;
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
     * @return \lispa\amos\report\models\Report
     */
    public function getWrappedObject()
    {
        return \lispa\amos\report\models\Report::findOne($this->id);
    }


}