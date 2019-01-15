<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    piattaforma-openinnovation
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\record;


interface NotifyRecordInterface
{
    public function isNews();
    public function createOrderClause();
    public function sendNotification();
}