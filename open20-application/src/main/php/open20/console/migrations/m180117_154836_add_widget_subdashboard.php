<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community\migrations
 * @category   CategoryName
 */

use lispa\amos\community\models\Community;
use yii\db\Migration;

/**
 * Class m171219_111336_add_community_field_hits
 */
class m180117_154836_add_widget_subdashboard extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->insert('amos_widgets', [
            'classname' => 'lispa\amos\admin\widgets\icons\WidgetIconMyProfile',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
            'default_order' => 1,
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function safeDown()
    {

        $this->delete('amos_widgets', [
            'classname' => 'lispa\amos\admin\widgets\icons\WidgetIconMyProfile',
            'type' => 'ICON',
            'module' => 'community',
            'status' => 1,
            'dashboard_visible' => 0,
            'sub_dashboard' => 1,
            'default_order' => 1,
        ]);
    }
}
