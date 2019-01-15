<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\rules
 * @category   CategoryName
 */

namespace lispa\amos\admin\rules;

use lispa\amos\admin\models\UserProfile;
use yii\rbac\Rule;

/**
 * Class ValidatedBasicUserRule
 * @package lispa\amos\admin\rules
 */
class ValidatedBasicUserRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'validatedBasicUser';
    
    /**
     * @inheritdoc
     */
    public function execute($loggedUserId, $item, $params)
    {
        /** @var UserProfile $loggedUser */
        $loggedUser = \Yii::$app->getUser()->identity->profile;
        return ($loggedUser->validato_almeno_una_volta == true);
    }
}
