<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\rbac
 * @category   CategoryName
 */

namespace lispa\amos\admin\rbac;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Class UpdateOwnUserProfile
 * @package lispa\amos\admin\rbac
 */
class UpdateOwnUserProfile extends Rule
{
    public $name = 'isYourProfile';
    public $description = '';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $model = ((isset($params['model']) && $params['model']) ? $params['model'] : new UserProfile());

        if (!$model->id) {
            $post = \Yii::$app->getRequest()->post();
            $get = \Yii::$app->getRequest()->get();

            if (isset($get['id'])) {
                $model = $this->instanceModel($model, $get['id']);
            } elseif (isset($post['id'])) {
                $model = $this->instanceModel($model, $post['id']);
            }
        }

        return ($model->user_id == $user);
    }

    /**
     * @param UserProfile $model
     * @param int $modelId
     * @return mixed
     */
    private function instanceModel($model, $modelId)
    {
        /** @var UserProfile $userProfileInstance */
        $userProfileInstance = AmosAdmin::instance()->createModel('UserProfile');
        $instancedModel = $userProfileInstance::findOne($modelId);
        if (!is_null($instancedModel)) {
            $model = $instancedModel;
        }
        return $model;
    }
}
