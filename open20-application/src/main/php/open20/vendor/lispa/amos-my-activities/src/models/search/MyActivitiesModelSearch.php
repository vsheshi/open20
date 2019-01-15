<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\models\search
 * @category   CategoryName
 */

namespace lispa\amos\myactivities\models\search;

use lispa\amos\myactivities\AmosMyActivities;
use lispa\amos\myactivities\basic\MyActivitiesModelsInterface;
use yii\base\Model;

class MyActivitiesModelSearch extends Model implements MyActivitiesModelsInterface
{
    public $createdAt;
    public $updatedAt;
    public $creatorNameSurname;
    public $searchString;
    public $orderType;
    public $wrappedObject;

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return mixed
     */
    public function getCreatorNameSurname()
    {
        return $this->creatorNameSurname;
    }

    /**
     * @return mixed
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    public function getWrappedObject()
    {
        return $this->wrappedObject;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['searchString'], 'safe'],
            [['orderType'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'searchString' => AmosMyActivities::t('amosmyactivities', 'Text search'),
            'orderType' => AmosMyActivities::t('amosmyactivities', 'Update date sort'),
        ];
    }

}