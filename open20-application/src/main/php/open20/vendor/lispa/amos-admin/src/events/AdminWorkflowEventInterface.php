<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\events
 * @category   CategoryName
 */

namespace lispa\amos\admin\events;

use yii\base\Event;

interface AdminWorkflowEventInterface
{
    /**
     * This method assign CREATOR_... roles to users with status ATTIVOEVALIDATO
     *
     * @param Event $event
     */
    public function assignCreatorRoles(Event $event);
}
