<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\socialauth
 * @category   CategoryName
 */

namespace lispa\amos\socialauth\controllers;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\attachments\components\FileImport;
use lispa\amos\core\user\User;
use lispa\amos\socialauth\models\SocialAuthUsers;
use lispa\amos\socialauth\Module;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UrlManager;
use yii\helpers\ArrayHelper;

/**
 * Class FileController
 * @package lispa\amos\socialauth\controllers
 */
class SocialAuthController extends Controller
{
    /**
     * @var string $layout
     */
    public $layout = 'login';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'link-user',
                            'unlink-user',
                        ],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'endpoint',
                            'sign-in',
                            'sign-up',
                        ],
                        //'roles' => ['*']
                    ]
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init() {

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
    }

    /**
     * Endpoint bridge for auth actions
     *
     * @param $action
     * @param $provider
     * @return \Hybrid_Endpoint
     */
    public function actionEndpoint($action, $provider)
    {
        $key = 'hauth_' . $action; // either `hauth_start` or `hauth_done`
        $_REQUEST[$key] = $provider; // provider will be something like `facebook` or `google`

        $adapter = \Hybrid_Endpoint::process();

        return $adapter;
    }

    /**
     * @param $provider
     * @return \Hybrid_Provider_Adapter
     */
    public function authProcedure($provider)
    {
        /**
         * @var $baseUrl string with the base url
         */
        $baseUrl = Yii::$app->request->getHostInfo();

        /**
         * @var $config Array with all configurations
         */
        $config = [
            'base_url' => $baseUrl,
            'providers' => $this->module->getProviders()
        ];

        /**
         * @var $callbackUrl string The full call back url to use in the provider
         */
        $callbackUrl = $baseUrl . '/socialauth/social-auth/endpoint';

        try {
            /**
             * @var $hybridauth Hybrid_Auth
             */
            $hybridauth = new \Hybrid_Auth($config);
        } catch (\Exception $e) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Login Failed'));

            return false;
        }

        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
        $adapter = $hybridauth->authenticate($provider, array(
            'login_start' => $callbackUrl . '?action=start&provider=' . strtolower($provider),
            'login_done' => $callbackUrl . '?action=done&provider=' . strtolower($provider),
        ));

        return $adapter;
    }

    /**
     * Login with social account
     * @param $provider
     * @return bool|\yii\web\Response
     */
    public function actionSignIn($provider)
    {
        /**
         * If the user is already logged in go to home
         */
        if (!Yii::$app->user->isGuest) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Already Logged In'));

            return $this->goHome();
        }

        /**
         * If login is not enabled
         */
        if (!$this->module->enableLogin) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Login Disabled'));

            return $this->goHome();
        }

        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
        $adapter = $this->authProcedure($provider);

        /**
         * If the adapter is not set go back to home
         */
        if (!$adapter) {
            return $this->goHome();
        }

        /**
         * @var $userProfile \Hybrid_User_Profile
         */
        $userProfile = $adapter->getUserProfile();

        /**
         * Kick off social user
         */
        $adapter->logout();

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = SocialAuthUsers::findOne(['identifier' => $userProfile->identifier, 'provider' => $provider]);

        /**
         * If the social user exists
         */
        if ($socialUser) {
            /**
             * If the user exists
             */
            if ($socialUser->user && $socialUser->user->id) {
                $loginTimeout = Yii::$app->params['loginTimeout'] ?: 3600;

                //Check user deactivated
                if ($socialUser->user->status == User::STATUS_DELETED) {
                    Yii::$app->session->addFlash('danger', Module::t('amosadmin', 'User deactivated. To log in again, request reactivation of the profile.'));
                    return $this->goHome();
                }

                $signIn = Yii::$app->user->login($socialUser->user, $loginTimeout);

                return $this->goHome();
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to Login with this User'));
            }

            return $this->goHome();
        } else {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'User Not Found, Please try with Other User'));

            return $this->goHome();
        }
    }

    /**
     * @param $provider
     * @return bool|\yii\web\Response
     */
    public function actionSignUp($provider)
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Already Logged In'));

            return $this->goHome();
        }

        /**
         * If signup is not enabled
         */
        if (!$this->module->enableRegister) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Signup Disabled'));

            return $this->goHome();
        }

        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
        $adapter = $this->authProcedure($provider);

        /**
         * If the mail is not set i can't create user
         */
        if (!$adapter) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to register, permission denied'));

            return $this->goHome();
        }

        /**
         * @var $socialProfile \Hybrid_User_Profile
         */
        $socialProfile = $adapter->getUserProfile();

        /**
         * Kick off social user
         */
        $adapter->logout();

        /**
         * If the mail is not set i can't create user
         */
        if (empty($socialProfile->email)) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to register, missing mail permission'));

            return $this->goHome();
        }

        //Create user and user profile
        $userCreated = $this->createUser($socialProfile);

        /**
         * If user creation fails
         */
        if (!$userCreated || isset($userCreated['error'])) {
            return $this->goHome();
        }

        /**
         * @var $user User
         */
        $user = $userCreated['user'];

        /**
         * @var $userProfile UserProfile
         */
        $userProfile = UserProfile::findOne(['user_id' => $user->id]);

        /**
         * If $newUser is false the user is not created
         */
        if (!$userProfile || !$userProfile->id) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Error when loading profile data, try again'));

            //Rollback User on error
            $user->id->delete();

            return $this->goHome();
        }

        //Import user profile image
        $importProfile = $this->importUserImage($socialProfile, $userProfile);

        //Back to home on error
        if (!$importProfile) {
            //Rollback on error
            $this->rollBackUser($userProfile);

            return $this->goHome();
        }

        //Create social record
        $socialUser = $this->createSocialUser($userProfile, $socialProfile, $provider);

        //If social profile creation fails
        if (!$socialUser || isset($socialUser['error'])) {
            //Rollback on error
            $this->rollBackUser($userProfile);
            return $this->goHome();
        }

        //Change login timeout
        $loginTimeout = Yii::$app->params['loginTimeout'] ?: 3600;

        //Logijn to the platform
        $signIn = Yii::$app->user->login($socialUser->user, $loginTimeout);

        return $this->goHome();
    }

    /**
     * @param $userProfile UserProfile
     */
    protected function rollBackUser($userProfile)
    {
        /**
         * @var $user User
         */
        $user = $userProfile->user;

        //Delete user profile
        $userProfile->delete();

        //Delete User
        $user->delete();

        return true;
    }

    /**
     * @param \Hybrid_User_Profile $socialProfile
     * @return bool|int
     */
    protected function createUser(\Hybrid_User_Profile $socialProfile)
    {
        try {
            //Name Parts (maybe it contains last name
            $userNameParts = explode(' ', $socialProfile->firstName);

            //If the name is explodable generate last name
            if (count($userNameParts)) {
                if (!$socialProfile->lastName) {
                    //This contains only name parts
                    $copyParts = $userNameParts;

                    //Only name part
                    $userName = reset($userNameParts);

                    //Shift out name
                    array_shift($copyParts);

                    //Last name of user (or rollback to first name)
                    $userSurname = count($copyParts) ? implode(' ', $copyParts) : $userName;
                } else {
                    //Name of the user
                    $userName = $socialProfile->firstName;

                    //Last name of user
                    $userSurname = $socialProfile->lastName;
                }
            } else {
                $userName = 'User';
                $userSurname = $socialProfile->email;
            }


            /**
             * @var $newUser integer False or UserId
             */
            $newUser = AmosAdmin::createNewAccount(
                $userName,
                $userSurname,
                $socialProfile->email,
                true
            );

            //If $newUser is false the user is not created
            if (!$newUser || isset($newUser['error'])) {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to register, user creation error'));

                if ($newUser['messages']) {
                    foreach ($newUser['messages'] as $message) {
                        Yii::$app->session->addFlash('danger', Module::t('amossocialauth', reset($message)));
                    }
                }

                return false;
            }

            return $newUser;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param \Hybrid_User_Profile $socialProfile
     * @param $userProfile
     * @return bool
     */
    protected function importUserImage(\Hybrid_User_Profile $socialProfile, $userProfile)
    {
        //If profile image url is set
        if ($socialProfile->photoURL) {
            //Request file header
            $fileHeader = @get_headers($socialProfile->photoURL);

            //If the file exists (header 200)
            if (preg_match("|200|", $fileHeader[0]) || preg_match("|304|", $fileHeader[0]) || preg_match("|302|", $fileHeader[0])) {
                // Get Importer component
                $importTool = new FileImport();

                //The Image content
                $temporaryFile = $this->obtainImage($socialProfile->photoURL);

                if($temporaryFile == false) {
                    Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to store image file, try again'));

                    return false;
                }

                //Import file as avatar
                $importResult = $importTool->importFileForModel($userProfile, 'userProfileImage', $temporaryFile);

                if (isset($importResult['error'])) {
                    Yii::$app->session->addFlash('danger', Module::t('amossocialauth', $importResult['error']));
                    return false;
                } elseif ($importResult == false) {
                    Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to import the user avatar'));
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $fileUrl
     * @return string
     */
    protected function obtainImage($fileUrl)
    {
        try {
            //Temporary file path
            $filepath = '/tmp/' . md5($fileUrl);

            //Obtain File Data
            $fileData = file_get_contents($fileUrl);

            //Put content to temporary dir
            file_put_contents($filepath, $fileData);

            //Change permissions
            @chmod($filepath, 0777);

            return $filepath;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param UserProfile $userProfile
     * @param \Hybrid_User_Profile $socialProfile
     * @param $provider
     * @return bool|SocialAuthUsers
     */
    protected function createSocialUser($userProfile, \Hybrid_User_Profile $socialProfile, $provider)
    {
        try {
            /**
             * @var $socialUser SocialAuthUsers
             */
            $socialUser = new SocialAuthUsers();

            /**
             * @var $socialProfileArray array User profile from provider
             */
            $socialProfileArray = (array)$socialProfile;
            $socialProfileArray['provider'] = $provider;
            $socialProfileArray['user_id'] = $userProfile->user_id;

            /**
             * If all data can be loaded to new record
             */
            if ($socialUser->load(['SocialAuthUsers' => $socialProfileArray])) {
                /**
                 * Is valid social user
                 */
                if ($socialUser->validate()) {
                    $socialUser->save();
                    return $socialUser;
                } else {
                    Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to Link The Social Profile'));
                    return false;
                }
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Invalid Social Profile, Try again'));
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Link current logged user to social account
     * @param $provider
     * @return \yii\web\Response
     */
    public function actionLinkUser($provider)
    {
        $this->setUpLayout('login');

        /**
         * If the user is already logged in go to home
         */
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Please LogIn to your account First'));

            return $this->goHome();
        }

        /**
         * If linking is not enabled
         */
        if (!$this->module->enableLink) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Linking Disabled'));

            return $this->goBack();
        }

        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
        $adapter = $this->authProcedure($provider);

        /**
         * If the adapter is not set go back to home
         */
        if (!$adapter) {
            return $this->goBack();
        }

        /**
         * @var $userProfile \Hybrid_User_Profile
         */
        $userProfile = $adapter->getUserProfile();

        /**
         * Kick off social user
         */
        $adapter->logout();

        /**
         * Find for existing social profile with the same ID
         * @var $existingUserProfile SocialAuthUsers
         */
        $existingUserProfile = SocialAuthUsers::findOne(['identifier' => $userProfile->identifier, 'provider' => $provider]);

        /**
         * If the social profile exists go back with notice
         */
        if ($existingUserProfile && $existingUserProfile->id) {
            if ($existingUserProfile->user_id == Yii::$app->user->id) {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Profile Already Connected'));
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Profile Already Connected to Another User'));
            }

            return $this->goBack();
        }

        /**
         * @var $userProfileArray array User profile from provider
         */
        $userProfileArray = (array)$userProfile;
        $userProfileArray['provider'] = $provider;
        $userProfileArray['user_id'] = Yii::$app->user->id;

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = new SocialAuthUsers();

        /**
         * If all data can be loaded to new record
         */
        if ($socialUser->load(['SocialAuthUsers' => $userProfileArray])) {
            /**
             * Is valid social user
             */
            if ($socialUser->validate()) {
                $socialUser->save();

                Yii::$app->session->addFlash('success', Module::t('amossocialauth', 'Social profile Linked'));

                return $this->goBack();
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to Link The Social Profile'));

                return $this->goBack();
            }
        } else {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Invalid Social Profile, Try again'));

            return $this->goBack();
        }
    }

    /**
     * UnLink current logged user to social account
     * @param $provider
     * @return \yii\web\Response
     */
    public function actionUnlinkUser($provider)
    {
        $this->setUpLayout('login');

        /**
         * If the user is already logged in go to home
         */
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Please LogIn to your account First'));

            return $this->goHome();
        }

        /**
         * If linking is not enabled
         */
        if (!$this->module->enableLink) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social Linking Disabled'));

            return $this->goBack();
        }

        /**
         * @var $socialUser SocialAuthUsers
         */
        $socialUser = SocialAuthUsers::findOne([
            'user_id' => Yii::$app->user->id,
            'provider' => $provider
        ]);

        /**
         * If linking is not enabled
         */
        if (!$socialUser || !$socialUser->id) {
            Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Social User Not Found'));

            return $this->goBack();
        }


        //If found delete and go back
        $socialUser->delete();

        //Reponse good state
        Yii::$app->session->addFlash('success', Module::t('amossocialauth', 'Social Account Unlinked'));

        //Go back
        return $this->goBack();
    }

    /**
     * @param null $layout
     * @return bool
     */
    public function setUpLayout($layout = null){
        if ($layout === false){
            $this->layout = false;
            return true;
        }
        $module = \Yii::$app->getModule('layout');
        if(empty($module)){
            $this->layout =  '@vendor/lispa/amos-core/views/layouts/' . (!empty($layout) ? $layout : $this->layout);
            return true;
        }
        $this->layout = (!empty($layout)) ? $layout : $this->layout;
        return true;
    }
}
