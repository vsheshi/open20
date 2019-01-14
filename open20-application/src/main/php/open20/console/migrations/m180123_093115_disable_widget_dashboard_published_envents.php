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
class m180123_093115_disable_widget_dashboard_published_envents extends \yii\db\Migration
{


    public function safeUp()
    {
        $this->update('amos_widgets', ['status' => '0'], ['classname' => 'lispa\amos\events\widgets\icons\WidgetIconEventsToPublish']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('amos_widgets', ['status' => '1'], ['classname' => 'lispa\amos\events\widgets\icons\WidgetIconEventsToPublish']);
        return true;
    }
}