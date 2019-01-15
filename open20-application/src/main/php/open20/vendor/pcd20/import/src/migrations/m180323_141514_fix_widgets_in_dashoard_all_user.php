<?php

use yii\db\Migration;

class m180323_141514_fix_widgets_in_dashoard_all_user extends Migration
{

    public function safeUp()
    {
        $this->update('amos_widgets',['dashboard_visible' => 0],['classname' => 'lispa\amos\discussioni\widgets\graphics\WidgetGraphicsUltimeDiscussioni', 'module' => 'discussioni']);
        $this->update('amos_widgets',['dashboard_visible' => 0],['classname' => 'lispa\amos\documenti\widgets\graphics\WidgetGraphicsUltimiDocumenti', 'module' => 'documenti']);
        $this->update('amos_widgets',['dashboard_visible' => 0],['classname' => 'lispa\amos\news\widgets\graphics\WidgetGraphicsUltimeNews', 'module' => 'news']);

    }

    public function safeDown()
    {
        $this->update('amos_widgets',['dashboard_visible' => 1],['classname' => 'lispa\amos\discussioni\widgets\graphics\WidgetGraphicsUltimeDiscussioni', 'module' => 'discussioni']);
        $this->update('amos_widgets',['dashboard_visible' => 1],['classname' => 'lispa\amos\documenti\widgets\graphics\WidgetGraphicsUltimiDocumenti', 'module' => 'documenti']);
        $this->update('amos_widgets',['dashboard_visible' => 1],['classname' => 'lispa\amos\news\widgets\graphics\WidgetGraphicsUltimeNews', 'module' => 'news']);

    }

}
