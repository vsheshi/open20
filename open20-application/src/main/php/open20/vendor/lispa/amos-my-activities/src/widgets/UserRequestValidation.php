<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\widgets
 * @category   CategoryName
 */

namespace lispa\amos\myactivities\widgets;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\interfaces\WorkflowModelInterface;
use lispa\amos\core\record\Record;

/**
 * Class UserRequestValidation
 */
class UserRequestValidation extends \yii\base\Widget
{
    /** @var  Record $model */
    public $model;

    /** @var string $labelKey - label for activity title translation*/
    public $labelKey;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $model = $this->model->getWrappedObject();
        $workflowModule = \Yii::$app->getModule('workflow');
        if ($workflowModule) {
            /** @var  WorkflowModelInterface $model */
            $userId = $model->getStatusLastUpdateUser($model->getToValidateStatus());
            $validationRequestTime = $model->getStatusLastUpdateTime($model->getToValidateStatus());
        } 
        if (is_null($userId)) {
            $userId = $model->updated_by;
            if (is_null($userId)) {
                $userId = $model->created_by;
            }
            $validationRequestTime = $model->updated_at;
            if (is_null($validationRequestTime)) {
                $validationRequestTime = $model->created_at;
            }
        }
        if (!is_null($userId)) {
            $userProfile = UserProfile::find()->andWhere(['user_id' => $userId])->one();
            if (!is_null($userProfile)) {
                return $this->render('user_request_validation', [
                    'userProfile' => $userProfile,
                    'model' => $this->model,
                    'validationRequestTime' => $validationRequestTime,
                    'labelKey' => $this->labelKey
                ]);
            }
        }
        return '';
    }



}