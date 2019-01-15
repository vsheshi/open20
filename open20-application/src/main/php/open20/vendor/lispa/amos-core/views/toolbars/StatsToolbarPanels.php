<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\views\toolbars
 * @category   CategoryName
 */

namespace lispa\amos\core\views\toolbars;

use lispa\amos\core\icons\AmosIcons;
use Yii;

class StatsToolbarPanels
{

    /**
     * @param $model
     * @param $count
     * @return array
     */
    public static function getCommentsPanel($model,$count){
        return array('comments' => new CommentStatsPanel([
            'icon' => AmosIcons::show('comments'),
            'label' => '',
            'description' => \Yii::t('amoscore', '#StatsBar_Interventions'),
            'count' => $count,
            'url' => Yii::$app->getUrlManager()->createUrl([
                $model->getViewUrl(),
                'id' => $model->getPrimaryKey(),
                '#' => 'comments_anchor',
            ])
        ]));
    }

    /**
     * @param $model
     * @param $count
     * @return array
     */
    public static function getTagsPanel($model,$count){
       return  array('tags' =>  new StatsPanel([
            'icon' => AmosIcons::show('label'),
            'label' => '',
            'count' => $count,
            'description' => Yii::t('amoscore', '#StatsBar_Tags'),
            'url' => Yii::$app->getUrlManager()->createUrl([
                $model->getViewUrl(),
                'id' => $model->getPrimaryKey(),
                '#' => 'tab-classifications'
            ])
        ]));
    }

    /**
     * @param $model
     * @param $count
     * @return array
     */
    public static function getDocumentsPanel($model,$count){
        return array('documents' =>  new StatsPanel([
            'icon' => AmosIcons::show('attachment'),
            'label' => '',
            'count' =>$count, //calculate only attach and not principal files.
            'description' => Yii::t('amoscore', '#StatsBar_Attachments'),
            'url' => \Yii::$app->getUrlManager()->createUrl([
                $model->getViewUrl(),
                'id' => $model->getPrimaryKey(),
                '#' => 'tab-attachments'
            ])
        ]));
    }
}