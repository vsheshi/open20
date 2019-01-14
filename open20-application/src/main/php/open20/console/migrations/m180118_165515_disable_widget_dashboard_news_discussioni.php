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
class m180118_165515_disable_widget_dashboard_news_discussioni extends \yii\db\Migration
{


    public function safeUp()
    {
        $this->update('amos_widgets', ['status' => '0'], ['classname' => 'lispa\amos\news\widgets\icons\WidgetIconNewsDaValidare']);
        $this->update('amos_widgets', ['status' => '0'], ['classname' => 'lispa\amos\news\widgets\icons\WidgetIconAllNews']);
        $this->update('amos_widgets', ['status' => '0'], ['classname' => 'lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopicAll']);
        $this->update('amos_widgets', ['status' => '0'], ['classname' => 'lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopicDaValidare']);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('amos_widgets', ['status' => '1'], ['classname' => 'lispa\amos\news\widgets\icons\WidgetIconNewsDaValidare']);
        $this->update('amos_widgets', ['status' => '1'], ['classname' => 'lispa\amos\news\widgets\icons\WidgetIconAllNews']);
        $this->update('amos_widgets', ['status' => '1'], ['classname' => 'lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopicAll']);
        $this->update('amos_widgets', ['status' => '1'], ['classname' => 'lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopicDaValidare']);
        return true;
    }
}