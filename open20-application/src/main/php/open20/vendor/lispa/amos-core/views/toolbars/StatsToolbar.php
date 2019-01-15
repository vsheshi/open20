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

use lispa\amos\core\interfaces\StatsToolbarInterface;
use yii\base\Widget;
use yii\base\Model;

/**
 * Class StatsToolbar
 *
 * @package lispa\amos\core\views\toolbars
 *
 */
class StatsToolbar extends Widget
{
    const BEHAVIORS_METHOD_EXPOSED = 'getStatsToolbar';

    /**
     * @var Model the data model that this widget is associated with.
     */
    public $model;

    /**
     * @var array
     */
    public $panels = [];

    /**
     * @var bool
     */
    public $onClick = false;

    /**
     *
     */
    public function init()
    {
        $moduleL = \Yii::$app->getModule('layout');
        if (!empty($moduleL)) {
            \lispa\amos\layout\assets\TabsAsset::register($this->getView());
        } else {
            \lispa\amos\core\views\assets\TabsAsset::register($this->getView());
        }
        $this->panels = $this->fetchPanels();
        parent::init();
    }

    /**
     *
     * @return array
     */
    protected function fetchPanels()
    {
        $panels = [];

        if ($this->model instanceof StatsToolbarInterface) {
            $panels = $this->model->{self::BEHAVIORS_METHOD_EXPOSED}();
        }
        return $panels;
    }

    /**
     *
     */
    public function run()
    {
        return $this->render('toolbar',
                [
                'panels' => $this->panels,
                'model' => $this->model,
                'onClick' => $this->onClick
        ]);
    }
}