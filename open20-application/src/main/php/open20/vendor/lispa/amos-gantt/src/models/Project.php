<?php

namespace lispa\amos\gantt\models;

use yii\base\Event;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Projects
 * @package lispa\amos\gantt\models
 *
 * @var integer $id
 * @var string $text
 * @var datetime $start_date
 * @var integer $order
 * @var float $progress
 * @var boolean $open
 * @var integer $parent
 * @var array $custom
 * @var array $links
 *
 */
class Project extends Model
{
    public static $TYPE_PROJECT = 'gantt.config.types.project';
    public static $PREFIX_ID = 'p-';

    public $id;
    public $text;
    public $order;
    public $open = true;
    public $parent;
    public $color = "#666666";
    public $textColor = "#FFFFFF";
    public $custom = [];
    public $links = [];

    public $asGanttValue = '{}';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'text',
                    'custom',
                    'links'
                ],
                'required'
            ],
            [
                [
                    'order',
                    'parent',
                ],
                'integer'
            ],
        ];

    }

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_VALIDATE, function (Event $event) {
            $event->sender->asGanttValue = Json::encode(
                ArrayHelper::merge(
                    $event->sender->toArray(),
                    [
                        'id' => self::$PREFIX_ID . $event->sender->id,
                        'text' => $event->sender->text,
                        'type' => self::$TYPE_PROJECT,
                        'readonly' => true
                    ]));
        });

    }

    /**
     * @param int $option
     * @return mixed
     */
    public function toJson($option = 302)
    {
        return Json::encode($this, $option);
    }

}