<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\utility
 * @category   CategoryName
 */

namespace lispa\amos\admin\utility;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\user\User;
use lispa\amos\core\utilities\Email;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;

/**
 * Class UserProfileUtility
 * @package lispa\amos\admin\utility
 */
class UserProfileUtility
{
    /**
     * Errors
     */
    const UNABLE_TO_CREATE_USER_ERROR = 1;
    const UNABLE_TO_CREATE_USER_PROFILE_ERROR = 2;
    const UNABLE_TO_ASSIGN_USER_ROLES_ERROR = 3;

    /**
     * This method return all facilitator user ids.
     * @return int[]
     */
    public static function getAllFacilitatorUserIds()
    {
        return \Yii::$app->getAuthManager()->getUserIdsByRole('FACILITATOR');
    }

    /**
     * The method create a new account. It creates a new User and new UserProfile only with name, surname
     * and email. The email must be unique in the database! It assign the BASIC_USER role to the new user.
     * This method returns the user id if all goes well. It returns boolean false in case of errors.
     * @param string $name
     * @param string $surname
     * @param string $email
     * @param string $community Community
     * @param int $privacy default Not Accepted
     * @return array Error or user id
     */
    public static function createNewAccount($name, $surname, $email, $privacy = 0, $sendCredentials = false, $community = null)
    {
        $user = self::createNewUser($email);

        if (!$user || $user->hasErrors()) {
            return [
                'error' => self::UNABLE_TO_CREATE_USER_ERROR,
                'messages' => $user->getErrors()
            ];
        }

        /**
         *
         */
        $userProfile = self::createNewUserProfile($user, $name, $surname, $privacy);

        if (!$userProfile || $userProfile->hasErrors()) {
            return [
                'error' => self::UNABLE_TO_CREATE_USER_PROFILE_ERROR,
                'messages' => $userProfile->getErrors()
            ];
        }

        self::setCwhPersonalValidation($userProfile);
        $ok = self::setBasicUserRoleToUser($user->id);

        if (!$ok) {
            return [
                'error' => self::UNABLE_TO_ASSIGN_USER_ROLES_ERROR
            ];
        }
        if ($sendCredentials) {
            self::sendCredentialsMail($userProfile, $community);
        }

        return ['user' => $user];
    }

    /**
     * This method create new User only with the email that must be unique.
     * @param string $email
     * @return User
     */
    public static function createNewUser($email)
    {
        /** @var User $user */
        $user = AmosAdmin::instance()->createModel('User');
        $user->status = User::STATUS_ACTIVE;
        $user->email = $email;
//        $user->username = $email;
        $user->save();

        return $user;
    }

    /**
     * This method create new UserProfile only with the name and surname.
     * @param User $user
     * @param string $name
     * @param string $surname
     * @param int $privacy default Not Accepted
     * @return UserProfile
     */
    public static function createNewUserProfile($user, $name, $surname, $privacy = 0)
    {
        /** @var AmosAdmin $adminModule */
        $adminModule = AmosAdmin::instance();
        /** @var UserProfile $userProfile */
        $userProfile = $adminModule->createModel('UserProfile');
        $userProfile->setScenario(UserProfile::SCENARIO_CREATE_NEW_ACCOUNT);
        $userProfile->user_id = $user->id;
        $userProfile->attivo = UserProfile::STATUS_ACTIVE;
        if ($adminModule->bypassWorkflow) {
            $userProfile->validato_almeno_una_volta = 1;
        }
        $userProfile->status = UserProfile::USERPROFILE_WORKFLOW_STATUS_DRAFT;
        $userProfile->nome = $name;
        $userProfile->cognome = $surname;
        $userProfile->privacy = $privacy;
        $userProfile->widgets_selected = 'a:2:{s:7:"primary";a:1:{i:0;a:6:{i:0;a:2:{s:4:"code";s:12:"USER_PROFILE";s:11:"module_name";s:5:"admin";}i:1;a:2:{s:4:"code";s:5:"USERS";s:11:"module_name";s:5:"admin";}i:2;a:2:{s:4:"code";s:11:"TAG_MANAGER";s:11:"module_name";s:3:"tag";}i:3;a:2:{s:4:"code";s:4:"ENTI";s:11:"module_name";s:4:"enti";}i:4;a:2:{s:4:"code";s:9:"ENTI_TIPO";s:11:"module_name";s:4:"enti";}i:5;a:2:{s:4:"code";s:4:"SEDI";s:11:"module_name";s:4:"enti";}}}s:5:"admin";a:1:{i:0;a:2:{i:0;a:2:{s:4:"code";s:12:"USER_PROFILE";s:11:"module_name";s:5:"admin";}i:1;a:2:{s:4:"code";s:5:"USERS";s:11:"module_name";s:5:"admin";}}}}';
        $userProfile->detachBehaviorByClassName(SimpleWorkflowBehavior::className());
        $userProfile->save();

        return $userProfile;
    }

    /**
     * Setting personal validation scope for contents if cwh module is enabled
     * @param UserProfile $userProfile
     */
    public static function setCwhPersonalValidation($userProfile)
    {
        // Setting personal validation scope for contents if cwh module is enabled
        $cwhModule = \Yii::$app->getModule('cwh');
        if (!empty($cwhModule)) {
            $cwhModelsEnabled = $cwhModule->modelsEnabled;
            foreach ($cwhModelsEnabled as $contentModel) {
                $permissionCreateArray = [
                    'item_name' => $cwhModule->permissionPrefix . "_CREATE_" . $contentModel,
                    'user_id' => $userProfile->user_id,
                    'cwh_nodi_id' => 'user-' . $userProfile->user_id
                ];
                // Add cwh permission to create content in 'Personal' scope
                $cwhAssignCreate = new \lispa\amos\cwh\models\CwhAuthAssignment($permissionCreateArray);
                $cwhAssignCreate->save(false);
            }
        }
    }

    /**
     * This method set the BASIC_USER role to the user id passed by parameters.
     * It return
     * @param int $userId
     * @return bool
     */
    public static function setBasicUserRoleToUser($userId)
    {
        /** @var AmosAdmin $adminModule */
        $adminModule = \Yii::$app->getModule(AmosAdmin::getModuleName());
        $basicUserRole = \Yii::$app->getAuthManager()->getRole($adminModule->defaultUserRole);
        if (is_null($basicUserRole)) {
            return false;
        }
        $ok = true;
        try {
            \Yii::$app->getAuthManager()->assign($basicUserRole, $userId);
        } catch (\Exception $exception) {
            $ok = false;
        }
        return $ok;
    }

    /**
     * This method return all communities to view for a single manager in the community managers list.
     * @param \lispa\amos\community\AmosCommunity $communityModule
     * @param int $userId
     * @return \lispa\amos\community\models\Community[]
     */
    public static function getCommunitiesForManagers($communityModule, $userId)
    {
        $allUserCommunities = $communityModule->getCommunitiesManagedByUserId($userId);
        $userCommunities = [];
        foreach ($allUserCommunities as $userCommunity) {
            if (
                ($userCommunity->community_type_id != \lispa\amos\community\models\CommunityType::COMMUNITY_TYPE_CLOSED)
                || $userCommunity->isNetworkUser($userCommunity->id)
                || \Yii::$app->user->can('ADMIN')
            ) {
                $userCommunities[] = $userCommunity;
            }
        }
        return $userCommunities;
    }

    /**
     * @param UserProfile $model
     * @param \lispa\amos\community\models\Community $model
     * @return bool
     */
    public static function sendCredentialsMail($model, $community = null)
    {
        try {
            $model->user->generatePasswordResetToken();
            $model->user->save(false);
            /** @var AmosAdmin $adminModule */
            $adminModule = \Yii::$app->getModule(AmosAdmin::getModuleName());
            $subjectView = $adminModule->htmlMailSubject;
            $contentView = $adminModule->htmlMailContent;
            $subject = Email::renderMailPartial($subjectView, ['profile' => $model], \Yii::$app->getUser()->id);
            $mail = Email::renderMailPartial($contentView, ['profile' => $model, 'community' => $community], \Yii::$app->getUser()->id);
            return Email::sendMail(Yii::$app->params['supportEmail'], [$model->user->email], $subject, $mail, []);
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return false;
    }

    /**
     * @param UserProfile $model
     * @param \lispa\amos\community\models\Community $model
     * @return bool
     */
    public static function sendAdminCredentialsMail($model, $community = null)
    {
        try {
            $model->user->generatePasswordResetToken();
            $model->user->save(false);
            /** @var AmosAdmin $adminModule */
            $adminModule = \Yii::$app->getModule(AmosAdmin::getModuleName());
            $subjectView = $adminModule->htmlSendCredentialMailSubject;
            $contentView = $adminModule->htmlSendCredentialMailContent;
            $subject = Email::renderMailPartial($subjectView, ['profile' => $model], \Yii::$app->getUser()->id);
            $mail = Email::renderMailPartial($contentView, ['profile' => $model, 'community' => $community], \Yii::$app->getUser()->id);
            return Email::sendMail(Yii::$app->params['supportEmail'], [$model->user->email], $subject, $mail, []);
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return false;
    }

    /**
     * @param UserProfile $model
     * @param \lispa\amos\community\models\Community $model
     * @return bool
     */
    public static function sendPasswordResetMail($model, $community = null)
    {
        try {
            $model->user->generatePasswordResetToken();
            $model->user->save(false);
            /** @var AmosAdmin $adminModule */
            $adminModule = \Yii::$app->getModule(AmosAdmin::getModuleName());
            $subjectView = '@vendor/lispa/amos-admin/src/mail/user/forgotpassword-subject';
            $contentView = '@vendor/lispa/amos-admin/src/mail/user/forgotpassword-html';
            $subject = Email::renderMailPartial($subjectView, ['profile' => $model], \Yii::$app->getUser()->id);
            $mail = Email::renderMailPartial($contentView, ['profile' => $model, 'community' => $community], \Yii::$app->getUser()->id);
            return Email::sendMail(Yii::$app->params['supportEmail'], [$model->user->email], $subject, $mail, []);
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return false;
    }

    /**
     * @param $model
     * @return string
     */
    public static function generateSubject($model)
    {
        $subject = AmosAdmin::t('amosadmin', '#welcome_man') . Yii::$app->name;
        if ($model->sesso == 'Femmina') {
            $subject = AmosAdmin::t('amosadmin', '#welcome_woman') . Yii::$app->name;
        }
        return $subject;
    }
}
