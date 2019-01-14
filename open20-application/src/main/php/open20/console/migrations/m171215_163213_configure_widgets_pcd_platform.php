<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    pcd20-platform
 * @category   CategoryName
 */

/**
 * Class m171215_163213_configure_widgets_pcd_platform
 */
class m171215_163213_configure_widgets_pcd_platform extends \yii\db\Migration
{

    /**
     * @throws MigrationsException
     */
    public function safeUp()
    {
        $widgets = [
            \lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDaValidare::className(),
            \lispa\amos\documenti\widgets\icons\WidgetIconDocumentiCategorie::className(),
            \lispa\amos\documenti\widgets\icons\WidgetIconAllDocumenti::className(),
            \lispa\amos\community\widgets\icons\WidgetIconToValidateCommunities::className(),
            \lispa\amos\community\widgets\icons\WidgetIconCreatedByCommunities::className(),
            \lispa\amos\community\widgets\icons\WidgetIconCommunity::className()
        ];
        $this->delete('amos_user_dashboards_widget_mm',['amos_widgets_classname' => $widgets]);
        $this->update('amos_widgets', ['status' => 0], ['classname' => $widgets]);
        return true;
    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $widgets = [
            \lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDaValidare::className(),
            \lispa\amos\documenti\widgets\icons\WidgetIconDocumentiCategorie::className(),
            \lispa\amos\documenti\widgets\icons\WidgetIconAllDocumenti::className(),
            \lispa\amos\community\widgets\icons\WidgetIconToValidateCommunities::className(),
            \lispa\amos\community\widgets\icons\WidgetIconCreatedByCommunities::className(),
            \lispa\amos\community\widgets\icons\WidgetIconCommunity::className()
        ];
        $this->delete('amos_user_dashboards_widget_mm',['amos_widgets_classname' => $widgets]);
        $this->update('amos_widgets', ['status' => 1], ['classname' => $widgets]);
        return true;
    }
}
