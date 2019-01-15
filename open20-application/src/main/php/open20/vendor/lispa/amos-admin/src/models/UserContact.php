<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\models
 * @category   CategoryName
 */

namespace lispa\amos\admin\models;

use lispa\amos\admin\models\base\UserContact as BaseUserContact;

/**
 * Class UserContact
 * @package lispa\amos\admin\models
 */
class UserContact extends BaseUserContact
{

    /**
     * Constants for user contant statuses
     */
    const STATUS_INVITED = "INVITED";
    const STATUS_ACCEPTED = "ACCEPTED";
    const STATUS_REFUSED = "REFUSED";

}