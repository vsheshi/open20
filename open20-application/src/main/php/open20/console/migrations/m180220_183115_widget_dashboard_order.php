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
 * Class m171216_163215_configure_status_validate
 */
class m180220_183115_widget_dashboard_order extends \yii\db\Migration
{


    public function safeUp()
    {
        $this->update('amos_widgets', ['default_order' => '1'], ['module' =>'community', 'classname' => 'lispa\amos\documenti\widgets\graphics\WidgetGraphicsHierarchicalDocuments']);
        $this->update('amos_widgets', ['default_order' => '2'], ['module' =>'community', 'classname' => 'lispa\amos\community\widgets\graphics\WidgetGraphicsMyCommunities']);
        $this->update('amos_widgets', ['default_order' => '3'], ['module' =>'community', 'classname' => 'lispa\amos\news\widgets\graphics\WidgetGraphicsUltimeNews']);
        $this->update('amos_widgets', ['default_order' => '4'], ['module' =>'community', 'classname' => 'lispa\amos\discussioni\widgets\graphics\WidgetGraphicsUltimeDiscussioni']);
        $this->update('amos_widgets', ['default_order' => '5'], ['module' =>'community', 'classname' => 'lispa\amos\admin\widgets\graphics\WidgetGraphicMyProfile']);

        $this->update('amos_widgets', ['default_order' => '1'], ['module' =>'community', 'classname' => 'lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard']);
        $this->update('amos_widgets', ['default_order' => '2'], ['module' =>'community', 'classname' => 'lispa\amos\news\widgets\icons\WidgetIconNewsDashboard']);
        $this->update('amos_widgets', ['default_order' => '3'], ['module' =>'community', 'classname' => 'lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniDashboard']);
        $this->update('amos_widgets', ['default_order' => '4'], ['module' =>'community', 'classname' => 'lispa\amos\events\widgets\icons\WidgetIconEvents']);
        $this->update('amos_widgets', ['default_order' => '6'], ['module' =>'community', 'classname' => 'lispa\amos\groups\widgets\icons\WidgetIconGroups']);
        $this->update('amos_widgets', ['status' => '0'], ['module' =>'community', 'classname' => 'lispa\amos\admin\widgets\icons\WidgetIconMyProfile']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('amos_widgets', ['status' => '1'], ['module' =>'community', 'classname' => 'lispa\amos\admin\widgets\icons\WidgetIconMyProfile']);

        return true;
    }
}