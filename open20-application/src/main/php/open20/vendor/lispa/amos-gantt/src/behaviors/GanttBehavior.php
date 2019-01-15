<?php
namespace lispa\amos\gantt\behaviors;

use lispa\amos\core\record\Record;
use lispa\amos\gantt\models\Link;
use lispa\amos\gantt\models\Project;
use lispa\amos\gantt\models\Task;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\Model;


/**
 * Class GanttBehavior
 *
 * @package lispa\amos\gantt\behavior
 */
class GanttBehavior extends Behavior
{

    /**
     * @var array|\Closure|null list of [[\lispa\amos\gantt\models\Projects]] objects.
     *
     * Each project can be specified as an array. For example:
     *
     * ```php
     * [
     *     'text' => 'My Project',
     *     'start_date' => '2016-06-12'
     *      ...
     * ]
     * ```
     * This field can be specified as a PHP callback of following signature:
     *
     * ```php
     * function (\yii\base\Model $model) {
     *     //return array projects
     * }
     * ```
     *
     * where `$model` is the current [[\yii\base\Model|\yii\base\ActiveRecord]] object owner.
     *
     * If this field is not set - an [[\yii\base\InvalidConfigException]] will be throwed.
     *
     */
    public $projects = [];
    public $tasks = [];
    public $links = [];

    /**
     * @var array list of [[lispa\amos\gantt\widgets\GanttWidget]] client events callbacks.
     *
     * Each callback must be specified as an array. For example:
     *
     * [
     *      'onBeforeLinkDelete' => [
     *          'url' => Yii::$app->getUrlManager()->createUrl(['delete-link']),
     *      ],
     *      'onBeforeLinkUpdate' => [
     *          'url' => Yii::$app->getUrlManager()->createUrl(['update-link'])
     *      ],
     *      'onBeforeLinkAdd' => [
     *          'url' => Yii::$app->getUrlManager()->createUrl(['add-link'])
     *      ],
     * ]
     *
     * 'url' will be called via ajax call
     *
     *
     */
    public $ajaxCallback = [];

    private $ganttValue = '{}';

    /**
     * @return string
     */
    public function getGanttValue()
    {
        return $this->ganttValue;
    }

    /**
     * @param string $ganttValue
     */
    public function setGanttValue($ganttValue)
    {
        $this->ganttValue = $ganttValue;
    }

    public function events()
    {
        return [
            Record::EVENT_AFTER_FIND => 'afterFind',
        ];
    }

    public function afterFind()
    {

        if ($this->config()) {
            $js = "var tasks = {
                    data : [
            ";
            /**@var Project $project * */
            foreach ($this->projects as $project) {
                $js .= $project->asGanttValue . ",";
            }

            /**@var Task $task * */
            foreach ($this->tasks as $task) {
                $js .= $task->asGanttValue . ",";
            }
            $js .= "],
                links : [";
            /**@var Link $link * */
            foreach ($this->links as $link) {
                $js .= $link->asGanttValue . ",";
            }

            $js .= "]";
            $js .= "};";
            $this->setGanttValue($js);
        }

    }

    protected function config()
    {
        if (!$this->owner) {
            return false;
        }

        $Projects = [];
        $ProjectsRet = [];
        $Tasks = [];
        $TasksRet = [];
        $Links = [];
        $LinksRet = [];

        $Projects = $this->projects;
        if ($this->projects instanceof \Closure) {
            $Projects = call_user_func($this->projects, $this->owner);
        }

        $Tasks = $this->tasks;
        if ($this->tasks instanceof \Closure) {
            $Tasks = call_user_func($this->tasks, $this->owner);
        }

        $Links = $this->links;
        if ($this->links instanceof \Closure) {
            $Links = call_user_func($this->links, $this->owner);
        }

        if (isset($Projects)) {
            foreach ($Projects as $project) {
                $ProjectTmp = new Project();

                if ($project instanceof Model) {
                    $ProjectTmp->setAttributes($project->attributes);
                } else {
                    $ProjectTmp->setAttributes($project);
                }
                if (!$ProjectTmp->validate()) {
                    throw new InvalidConfigException(\Yii::t('amosgantt', 'Error occurred during assign project'));
                }
                $ProjectsRet[] = $ProjectTmp;
            }
        }

        if (isset($Tasks)) {
            foreach ($Tasks as $task) {
                $TaskTmp = new Task($task);
                if ($TaskTmp->validate()) {
                    $TasksRet[] = $TaskTmp;
                } else {
                    throw new InvalidConfigException(\Yii::t('amosgantt', 'Error occurred during assign tasks'));
                }
            }
        }

        if (isset($Links)) {
            foreach ($Links as $link) {
                $LinkTmp = new Link($link);

                if ($LinkTmp->validate()) {
                    $LinksRet[] = $LinkTmp;
                } else {
                    throw new InvalidConfigException(\Yii::t('amosgantt', 'Error occurred during assign links'));
                }
            }
        }

        $this->projects = $ProjectsRet;
        $this->tasks = $TasksRet;
        $this->links = $LinksRet;

        if (!$Projects) {
            //throw new InvalidConfigException(\Yii::t('amosgantt', 'No Project selected'));
        }

        return true;

    }

}