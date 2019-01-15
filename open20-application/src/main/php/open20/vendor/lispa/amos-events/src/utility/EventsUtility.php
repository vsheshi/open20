<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\events\utility
 * @category   CategoryName
 */

namespace lispa\amos\events\utility;

use lispa\amos\attachments\models\File;
use lispa\amos\community\AmosCommunity;
use lispa\amos\community\exceptions\CommunityException;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityType;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\events\AmosEvents;
use lispa\amos\events\models\Event;
use lispa\amos\events\models\EventMembershipType;
use Yii;
use yii\log\Logger;

/**
 * Class EventsUtility
 * @package lispa\amos\events\utility
 */
class EventsUtility
{
    /**
     * This method translate the array values.
     * @param array $arrayValues
     * @return array
     */
    public static function translateArrayValues($arrayValues)
    {
        $translatedArrayValues = [];
        foreach ($arrayValues as $key => $title) {
            $translatedArrayValues[$key] = AmosEvents::t('amosevents', $title);
        }
        return $translatedArrayValues;
    }
    
    /**
     * Create a community for the event.
     * @param Event $model
     * @param string $managerStatus
     * @return bool
     */
    public static function createCommunity($model, $managerStatus = '')
    {
        /** @var AmosCommunity $communityModule */
        $communityModule = Yii::$app->getModule('community');
        $title = ($model->title ? $model->title : '');
        $description = ($model->description ? $model->description : '');
        $type = CommunityType::COMMUNITY_TYPE_CLOSED; // DEFAULT TYPE
        if ($model->event_membership_type_id == EventMembershipType::TYPE_OPEN) {
            $type = CommunityType::COMMUNITY_TYPE_PRIVATE;
        }
        if ($model->event_membership_type_id == EventMembershipType::TYPE_ON_INVITATION) {
            $type = CommunityType::COMMUNITY_TYPE_CLOSED;
        }
        $context = Event::className();
        $managerRole = $model->getManagerRole();
        try {
            $model->community_id = $communityModule->createCommunity($title, $type, $context, $managerRole, $description, $model, $managerStatus);
            $ok = $model->save(false);
            if (!is_null($model->community_id)) {
                $ok = EventsUtility::duplicateEventTagForCommunity($model);
            }
        } catch (CommunityException $exception) {
            \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
            $ok = false;
        }
        return $ok;
    }
    
    /**
     * Update a community.
     * @param Event $model
     * @return bool
     */
    public static function updateCommunity($model)
    {
        $model->community->name = $model->title;
        $model->community->description = $model->description;
        $ok = $model->community->save(false);
        return $ok;
    }
    
    /**
     * @param Event $model
     * @return bool
     */
    public static function duplicateEventTagForCommunity($model)
    {
        $moduleTag = Yii::$app->getModule('tag');
        $ok = true;
        if (isset($moduleTag) && in_array(Event::className(), $moduleTag->modelsEnabled) && $moduleTag->behaviors) {
            $eventTags = \lispa\amos\tag\models\EntitysTagsMm::findAll([
                'classname' => Event::className(),
                'record_id' => $model->id
            ]);
            foreach ($eventTags as $eventTag) {
                $entityTag = new \lispa\amos\tag\models\EntitysTagsMm();
                $entityTag->classname = Community::className();
                $entityTag->record_id = $model->community_id;
                $entityTag->tag_id = $eventTag->tag_id;
                $entityTag->root_id = $eventTag->root_id;
                $ok = $entityTag->save(false);
                if (!$ok) {
                    break;
                }
            }
        }
        return $ok;
    }
    
    /**
     * @param Event $model
     * @return bool
     */
    public static function duplicateEventLogoForCommunity($model)
    {
        $ok = true;
        $eventLogo = File::findOne(['model' => Event::className(), 'attribute' => 'eventLogo', 'itemId' => $model->id]);
        if (!is_null($eventLogo)) {
            $communityLogo = File::findOne(['model' => Community::className(), 'attribute' => 'communityLogo', 'itemId' => $model->community_id]);
            if (!is_null($communityLogo)) {
                if ($eventLogo->hash != $communityLogo->hash) {
                    $communityLogo->delete();
                    $ok = EventsUtility::newCommunityLogo($model->community_id, $eventLogo);
                }
            } else {
                $ok = EventsUtility::newCommunityLogo($model->community_id, $eventLogo);
            }
        } else {
            $communityLogo = File::findOne(['model' => Community::className(), 'attribute' => 'communityLogo', 'itemId' => $model->community_id]);
            if (!is_null($communityLogo)) {
                $communityLogo->delete();
            }
        }
        return $ok;
    }
    
    /**
     * @param int $communityId
     * @param File $eventLogo
     * @return bool
     */
    private static function newCommunityLogo($communityId, $eventLogo)
    {
        $communityLogo = new File();
        $eventLogoAttributes = $eventLogo->attributes;
        $toSkipFields = ['id', 'model', 'attribute', 'itemId'];
        foreach ($eventLogoAttributes as $fieldName => $fieldValue) {
            if (!in_array($fieldName, $toSkipFields)) {
                $communityLogo->{$fieldName} = $fieldValue;
            }
        }
        $communityLogo->model = Community::className();
        $communityLogo->attribute = 'communityLogo';
        $communityLogo->itemId = $communityId;
        return $communityLogo->save();
    }
    
    public static function deleteCommunityLogo($model)
    {
        $communityLogo = File::findOne(['model' => Community::className(), 'attribute' => 'communityLogo', 'itemId' => $model->community_id]);
        if (!is_null($communityLogo)) {
            $communityLogo->delete();
        }
    }
    
    /**
     * Check if there is at least one confirmed event manager only if there is a community. If not it return true.
     * @param Event $event
     * @return bool
     */
    public static function checkOneConfirmedManagerPresence($event)
    {
        if (!$event->community_id) {
            return true;
        }
        $confirmedEventManagers = self::findEventManagers($event, CommunityUserMm::STATUS_ACTIVE);
        return (count($confirmedEventManagers) > 0);
    }
    
    /**
     * Check if there is at least one confirmed event manager only if there is a community. If not it return true.
     * @param Event $event
     * @param string $status
     * @return array[CommunityUserMm]
     */
    public static function findEventManagers($event, $status = '')
    {
        if (!$event->community_id) {
            return [];
        }
        
        $where = [
            'community_id' => $event->community_id,
            'role' => $event->getManagerRole()
        ];
        
        if ($status) {
            $where['status'] = $status;
        }
        
        $eventManagers = CommunityUserMm::find()->andWhere($where)->all();
        
        return $eventManagers;
    }
}
