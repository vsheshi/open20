<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notificationmanager\record
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\record;

use Yii;
use yii\db\Expression;

/**
 * Class NotifyRecord
 * @package lispa\amos\notificationmanager\record
 */
class NotifyRecord extends \lispa\amos\core\record\Record implements NotifyRecordInterface
{
    /**
     * @var string $isNewsFiledName
     */
    protected static $isNewsFiledName = 'created_at';

    /**
     * @var array $mailStatuses - map workflow transitions for which an email must be sent with email configurations
     * [ end status =>  ChangeStatusEmail ]
     * for standard email when reaching toValidate and validated statuses, leave the array empty
     */
    public $mailStatuses = [];

    /**
     * @return bool
     */
    public function isNews()
    {
        $bool = false;
        $module = \Yii::$app->getModule('notify');
        if ($module) {
            $profile = \Yii::$app->getUser()->getIdentity()->getProfile();
            if ($this->__get(self::$isNewsFiledName) > $profile->ultimo_logout && !$module->modelIsRead($this, \Yii::$app->getUser()->id)) {
                $bool = true;
            }
        }
        return $bool;
    }

    /**
     * @return array
     */
    public function createOrderClause()
    {
        return [
            'attributes' => [
                $this->orderAttribute,
                'isNew' => [
                    'asc' => new Expression($this->tableName() . "." . self::$isNewsFiledName . " > '" . \Yii::$app->getUser()->getIdentity()->getProfile()->ultimo_logout . "'"),
                    'desc' => new Expression($this->tableName() . "." . self::$isNewsFiledName . " > '" . \Yii::$app->getUser()->getIdentity()->getProfile()->ultimo_logout . "' DESC"),
                    'default' => SORT_DESC,
                ],
                'id'
            ],
            'defaultOrder' => [
                'isNew' => SORT_DESC,
                $this->orderAttribute => (int)$this->orderType,
                'id' => SORT_DESC
            ]
        ];
    }

    /**
     * @return mixed|null
     */
    public function getNotifiedUserId()
    {
        $user_id = null;
        try {
            if ($this->hasAttribute('created_by')) {
                $user_id = $this->created_by;
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $user_id;
    }

    /**
     * @return bool
     */
    public function sendNotification()
    {
        return true;
    }
}
