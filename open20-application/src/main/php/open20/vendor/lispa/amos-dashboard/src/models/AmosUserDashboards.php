<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\dashboard
 * @category   CategoryName
 */

namespace lispa\amos\dashboard\models;

use yii\db\Exception;
use yii\db\Query;

/**
 * This is the model class for table "amos_user_dashboards".
 *
 * @property AmosWidgets[] $amosWidgetsSelectedIcon
 * @property AmosWidgets[] $amosWidgetsSelectedGraphic
 */
class AmosUserDashboards extends \lispa\amos\dashboard\models\base\AmosUserDashboards
{

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmosWidgetsSelectedIcon($forceAll = false)
    {
        $relQuery = $this->getWidgetBaseQuery()
            ->andWhere(['amos_widgets.type' => AmosWidgets::TYPE_ICON]);

        if ($forceAll == false) {
            $relQuery = $relQuery->andWhere(['dashboard_visible' => 1]);
        }
        return $relQuery;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    protected function getWidgetBaseQuery()
    {
        $relWidgetBaseQuery = $this->getAmosWidgetsClassnames()
            ->andWhere(['amos_widgets.status' => AmosWidgets::STATUS_ENABLED]);

        return $relWidgetBaseQuery;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmosWidgetsSelectedGraphic()
    {
        $relQuery = $this->getWidgetBaseQuery()
            ->andWhere(['amos_widgets.type' => AmosWidgets::TYPE_GRAPHIC]);
        if (\Yii::$app->getModule('dashboard')->useWidgetGraphicDashboardVisible == true) {
            $relQuery = $relQuery->andWhere(['dashboard_visible' => 1]);
        }
        if (\Yii::$app->getModule('dashboard')->useWidgetGraphicOrder == true) {
            $relQuery->orderBy('amos_widgets.default_order');
        }
        return $relQuery;
    }

    /**
     * @return bool
     */
    public function isPrimary()
    {
        return $this->slide == 1 && $this->module == 'dashboard';
    }

    /**
     * @return int
     */
    public function getMaxOrder()
    {
        $subquery = new Query();
        return $subquery->from(['subquery' => $this->getAmosWidgetsClassnames()])->max('widget_order');
    }
}