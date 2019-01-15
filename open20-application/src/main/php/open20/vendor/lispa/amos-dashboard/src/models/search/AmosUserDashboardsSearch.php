<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\dashboard
 * @category   CategoryName
 */

namespace lispa\amos\dashboard\models\search;

use lispa\amos\dashboard\models\AmosUserDashboards;
use yii\db\ActiveQuery;

/**
 * AmosUserDashboardsSearch represents the model behind the search form about `lispa\amos\dashboard\models\AmosUserDashboards`.
 */
class AmosUserDashboardsSearch extends AmosUserDashboards
{
    /**@return ActiveQuery */
    public function current($params)
    {
        $query = AmosUserDashboards::find();

        if (!($this->load($params) && $this->validate())) {
            return $query->andFilterWhere($params);
        }
    }

    public function rules()
    {
        return [
            [['id', 'user_id', 'slide', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['module', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

}