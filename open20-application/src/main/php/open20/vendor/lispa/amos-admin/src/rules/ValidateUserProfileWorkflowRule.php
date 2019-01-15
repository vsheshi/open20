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
use lispa\amos\core\rules\BasicContentRule;

/**
 * Class UpdateProfileFacilitatorRule
 * @package lispa\amos\admin\rules
 */
class ValidateUserProfileWorkflowRule extends BasicContentRule
{
    public $name = 'ValidateUserProfileWorkflow';
    
    /**
     * Facilitator cannot validate his own profile
     * @inheritdoc
     */
    public function ruleLogic($user, $item, $params, $model)
    {
        // Return false if the model is null
        if (is_null($model)) {
            return false;
        }

        /** @var UserProfile $model */
        if(\Yii::$app->user->can('FACILITATOR') && $model->user_id == $user){
            return false;
        }

        return true;
    }
}
