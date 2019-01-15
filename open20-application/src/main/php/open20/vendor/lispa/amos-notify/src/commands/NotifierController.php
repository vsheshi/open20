<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notificationmanager\commands
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\commands;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\user\User;
use lispa\amos\notificationmanager\AmosNotify;
use lispa\amos\notificationmanager\base\BuilderFactory;
use lispa\amos\notificationmanager\models\Notification;
use lispa\amos\notificationmanager\models\NotificationChannels;
use lispa\amos\notificationmanager\models\NotificationConf;
use lispa\amos\notificationmanager\models\NotificationsConfOpt;
use lispa\amos\notificationmanager\models\NotificationsRead;
use Exception;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * Class NotifierController
 * @package lispa\amos\notificationmanager\commands
 */
class NotifierController extends Controller
{
    public $weekMails = false;
    public $dayMails = false;
    public $monthMails = false;
    public $immediateMails = false;

    /**
     * @param string $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        return ['weekMails', 'dayMails', 'monthMails','immediateMails'];
    }

    /**
     *
     */
    public function actionMailChannel()
    {
        try {
            $type = $this->evaluateOpetions();
            Console::stdout('Begin mail-channel ' . $type . PHP_EOL);
            $users = $this->loadUser($type);
            $factory = new BuilderFactory();
            $builder = $factory->create(BuilderFactory::CONTENT_MAIL_BUILDER);
            $cwhModule = Yii::$app->getModule('cwh');

            foreach ($users as $user) {
                $uid = $user['user_id'];
                Console::stdout('Start working on user ' . $uid . PHP_EOL);
                $query = Notification::find()
                    ->leftJoin(NotificationsRead::tableName(), ['notification.id' => new \yii\db\Expression(NotificationsRead::tableName() . '.notification_id'), NotificationsRead::tableName() . '.user_id' => $uid])
                    ->andWhere(['channels' => NotificationChannels::CHANNEL_MAIL])
                    ->andWhere([NotificationsRead::tableName() . '.user_id' => null]);
                $notify = AmosNotify::instance();
                if (isset($notify->batchFromDate)) {
                    $query->andWhere(['>=', Notification::tableName() . '.created_at', strtotime($notify->batchFromDate)]);
                }

                if (isset($cwhModule)) {
                    $modelsEnabled = \lispa\amos\cwh\models\CwhConfigContents::find()->addSelect('classname')->column();
                    $andWhere = "";
                    $i = 0;
                    foreach ($modelsEnabled as $classname) {
                        $cwhActiveQuery = new \lispa\amos\cwh\query\CwhActiveQuery($classname, [
                            'queryBase' => $classname::find()->distinct(),
                            'userId' => $uid
                        ]);
                        $queryModel = $cwhActiveQuery->getQueryCwhOwnInterest();
                        $modelIds = $queryModel->select($classname::tableName() . '.id')->column();
                        if (!empty($modelIds)) {
                            if ($i != 0) {
                                $andWhere .= " OR ";
                            }
                            $andWhere .= '(' . Notification::tableName() . ".class_name = '" . addslashes($classname) . "' AND " . Notification::tableName() . '.content_id in (' . implode(',', $modelIds) . '))';
                            $i++;
                        }
                    }
                    if (!empty($andWhere)) {
                        $query->andWhere($andWhere);
                    }
                }
                $query->orderBy('class_name');

                $result = $query->all();
                if (!empty($result)) {
                    $builder->sendEmail([$uid], $result);
                }
                /** @var Notification $notify */
                foreach ($result as $notify) {
                    $this->notifyReadFlag($notify->id, $uid);
                }
                Console::stdout('End working on user ' . $uid . PHP_EOL);
            }
            Console::stdout('End mail-channel ' . $type . PHP_EOL);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     *
     */
    public function actionSMSChannel()
    {
        try {

        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param $notify_id
     * @param $reader_id
     */
    protected function notifyReadFlag($notify_id, $reader_id)
    {
        try {
            $model = new NotificationsRead();
            $model->notification_id = $notify_id;
            $model->user_id = $reader_id;
            $model->save();
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param $schedule
     * @return array|null
     */
    protected function loadUser($schedule)
    {
        $result = null;
        try {
            $query = new Query();
            $query->from(NotificationConf::tableName());
            $query->innerJoin(UserProfile::tableName(),
                NotificationConf::tableName() . '.user_id = ' . UserProfile::tableName() . '.user_id');
            $query->innerJoin(User::tableName(),
                UserProfile::tableName() . '.user_id = ' . User::tableName() . '.id');
            $query->where([UserProfile::tableName() . '.deleted_at' => null]);
            $query->andWhere([UserProfile::tableName() . '.attivo' => UserProfile::STATUS_ACTIVE]);
            $query->andWhere([User::tableName() . '.status' => User::STATUS_ACTIVE]);
            $query->andWhere([NotificationConf::tableName() . '.email' => $schedule]);
            $query->orderBy([NotificationConf::tableName() . '.user_id' => SORT_ASC]);
            $result = $query->all();
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * @return int
     */
    private function evaluateOpetions()
    {
        $retValue = NotificationsConfOpt::EMAIL_DAY;

        if ($this->dayMails) {
            $retValue = NotificationsConfOpt::EMAIL_DAY;
        } elseif ($this->weekMails) {
            $retValue = NotificationsConfOpt::EMAIL_WEEK;
        } elseif ($this->monthMails) {
            $retValue = NotificationsConfOpt::EMAIL_MONTH;
        }elseif ($this->immediateMails) {
            $retValue = NotificationsConfOpt::EMAIL_IMMEDIATE;
        }

        return $retValue;
    }
}
