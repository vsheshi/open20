<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\behaviors
 * @category   CategoryName
 */

namespace lispa\amos\core\behaviors;

use Yii;

/**
 * Class BlameableBehavior
 * @package lispa\amos\core\behaviors
 */
class BlameableBehavior extends \yii\behaviors\BlameableBehavior
{
    /**
     * @inheritdoc
     *
     * In case, when the [[value]] property is `null`, the value of `Yii::$app->user->id` will be used as the value.
     */
    protected function getValue($event)
    {
        if (!empty($this->attributes[$event->name]) && is_array($this->attributes[$event->name]) && in_array($this->createdByAttribute, $this->attributes[$event->name])) {
            if ($this->owner->{$this->createdByAttribute}) {
                return $this->owner->{$this->createdByAttribute};
            }
        }
        if ($this->value === null) {
            $user = Yii::$app->get('user', false);
            return $user && !$user->isGuest ? $user->id : null;
        }

        return parent::getValue($event);
    }
}
