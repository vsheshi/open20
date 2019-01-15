<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin
 * @category   CategoryName
 */

namespace lispa\amos\admin;

use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\admin\exceptions\AdminException;
use lispa\amos\admin\utility\UserProfileUtility;
use lispa\amos\admin\widgets\graphics\WidgetGraphicMyProfile;
use lispa\amos\admin\widgets\icons\WidgetIconMyProfile;
use lispa\amos\admin\widgets\icons\WidgetIconUserProfile;
use lispa\amos\core\module\AmosModule;

/**
 * Class AmosAdmin
 * @package lispa\amos\admin
 */
class AmosAdmin extends AmosModule
{
    const site_key_param = 'google_recaptcha_site_key';

    public $controllerNamespace = 'lispa\amos\admin\controllers';
    public $whiteListRoles = [];
    public $name = 'Utenti';
    public $searchListFields = [];
    /**
     * @var bool $enableRegister - set to true to enable user register to the application and create his own userprofile
     */
    public $enableRegister = false;
    /**
     * @var bool $enableUserContacts
     * enable connection to users, send private messages, and see 'contacts' in section 'NETWORK' of the user profile
     */
    public $enableUserContacts = true;

    /**
     * @var bool $cached - enable or not admin query caching
     */
    public $cached = false;
    /**
     * @var int $cacheDuration
     * seconds of query caching duration if $cache = true - default is 1 day
     */
    public $cacheDuration = 84600;

    /**
     * @var bool $bypassWorkflow If true the plugin bypass the user profile workflow and show nothing of it.
     */
    public $bypassWorkflow = false;

    /**
     * This is the html used to render the subject of the e-mail. In the view is available the variable $profile
     * that is an instance of 'lispa\amos\admin\models\UserProfile'
     * @var string
     */
    public $htmlMailSubject = '@vendor/lispa/amos-admin/src/mail/user/credenziali-subject';
    public $htmlSendCredentialMailSubject = '@vendor/lispa/amos-admin/src/mail/user/admin-credentials-subject';

    /**
     * This is the html used to render the message of the e-mail. In the view is available the variable $profile
     * that is an instance of 'lispa\amos\admin\models\UserProfile'
     * @var string
     */
    public $htmlMailContent = '@vendor/lispa/amos-admin/src/mail/user/credenziali-html';
    public $htmlSendCredentialMailContent = '@vendor/lispa/amos-admin/src/mail/user/admin-credentials-html';

    /**
     * This is the text used to render the message of the e-mail. In the view is available the variable $profile
     * that is an instance of 'lispa\amos\admin\models\UserProfile'
     * @var string
     */
    public $textMailContent = '@vendor/lispa/amos-admin/src/mail/user/credenziali-text';
    public $textSendCredentialMailContent = '@vendor/lispa/amos-admin/src/mail/user/admin-credentials-text';

    /**
     * @var array $fieldsConfigurations This array contains all configurations for boxes and fields.
     */
    public $fieldsConfigurations = [
        'boxes' => [
            'box_informazioni_base' => ['form' => true, 'view' => true]
        ],
        'fields' => [
            'nome' => ['form' => true, 'view' => true, 'referToBox' => 'box_informazioni_base'],
            'cognome' => ['form' => true, 'view' => true, 'referToBox' => 'box_informazioni_base'],
            'userProfileImage' => ['form' => true, 'view' => true, 'referToBox' => 'box_foto']
        ]
    ];

    /**
     * @var array $profileRequiredFields - mandatory fields in user profile form
     */
    public $profileRequiredFields = [
        'nome',
        'cognome',
        'status',
        'presentazione_breve'
    ];

    /**
     * @var bool $hideWidgetGraphicsActions
     */
    public $hideWidgetGraphicsActions = false;

    /**
     * @var ConfigurationManager $confManager
     */
    public $confManager = null;

    /**
     * At user creation, it is possible to customize the Rbac role to assign to a new user, default is BASIC_USER role.
     *
     * @var string $defaultUserRole
     */
    public $defaultUserRole = 'BASIC_USER';

    /**
     * This is the module name (you used as array key in modules configuration of your platform) referring to a module
     * extending lispa\amos\core\interfaces\OrganizationsModuleInterface
     * It is used to give the possibility to customize the entity type used to set user profile prevalent partnership, for example.
     *
     * @var string $organizationModuleName
     */
    private $organizationModuleName = "organizzazioni";

    /**
     * @return string
     */
    public function getOrganizationModuleName()
    {
        return $this->organizationModuleName;
    }

    /**
     * @param string $organizationModuleName
     */
    public function setOrganizationModuleName($organizationModuleName)
    {
        $this->organizationModuleName = $organizationModuleName;
    }

    /**
     * Module name
     * @return string
     */
    public static function getModuleName()
    {
        return 'admin';
    }

    /**
     * Module Initialization
     * @throws AdminException
     */
    public function init()
    {
        parent::init();

        \Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/controllers', __DIR__ . '/controllers/');
        // initialize the module with the configuration loaded from config.php
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));

        $this->confManager = new ConfigurationManager([
            'fieldsConfigurations' => $this->fieldsConfigurations
        ]);
        $this->confManager->checkFieldsConfigurationsStructure();

        //dependency injection of reCaptcha
        if (isset($this->reCaptcha)) {
            if (isset(\Yii::$app->params[self::site_key_param])) {
                $this->reCaptcha->siteKey = \Yii::$app->params[self::site_key_param];
            }
            \Yii::$app->set('reCaptcha', $this->reCaptcha);
        }

    }

    /**
     * Array of widget-namespaces that belong to the module
     * @return array
     */
    public function getWidgetGraphics()
    {
        return [
            WidgetGraphicMyProfile::className()
        ];
    }

    /**
     * Array of widget-namespaces that belong to the module
     * @return array
     */
    public function getWidgetIcons()
    {
        return [
            WidgetIconMyProfile::className(),
            WidgetIconUserProfile::className()
        ];
    }

    /**
     * Get roles white-list
     * @return array
     * @deprecated
     */
    public function getWhiteListRules() // TODO change to getWhiteListRoles()
    {
        trigger_error('Deprecated: this function is repleca by getWhiteListRoles', E_NOTICE);
        return $this->whiteListRoles;
    }

    /**
     * Return list of white Roles
     * @return []
     */
    public function getWhiteListRoles()
    {
        return $this->whiteListRoles;
    }

    /**
     * Get default models
     * @return array
     */
    protected function getDefaultModels()
    {
        return [
            'UserProfile' => __NAMESPACE__ . '\\' . 'models\UserProfile',
            'UserContact' => __NAMESPACE__ . '\\' . 'models\UserContact',
            'UserProfileStatiCivili' => __NAMESPACE__ . '\\' . 'models\UserProfileStatiCivili',
            'UserProfileTitoliStudio' => __NAMESPACE__ . '\\' . 'models\UserProfileTitoliStudio',
            'User' => 'lispa\amos\core\user\User',
            'IstatComuni' => 'lispa\amos\comuni\models\IstatComuni',
            'IstatProvince' => 'lispa\amos\comuni\models\IstatProvince',
            'IstatRegioni' => 'lispa\amos\comuni\models\IstatRegioni',
            'IstatNazioni' => 'lispa\amos\comuni\models\IstatNazioni',
            'Ruoli' => 'common\models\Ruoli',
            'UserProfileSearch' => __NAMESPACE__ . '\\' . 'models\search\UserProfileSearch',
            'UserContactSearch' => __NAMESPACE__ . '\\' . 'models\search\UserContactSearch',
            'UserProfileTitoliStudioSearch' => __NAMESPACE__ . '\\' . 'models\search\UserProfileTitoliStudioSearch',
        ];
    }

    /**
     * The method create a new account. It creates a new User and new UserProfile only with
     * name, surname and email. The email must be unique in the database! This method returns
     * the user id if all goes well. It returns boolean false in case of errors.
     * @param string $name
     * @param string $surname
     * @param string $email
     * @param \lispa\amos\community\models\Community $community
     * @param bool|false $sendCredentials if credential mail must be sent to the newly created user
     * @return array
     */
    public function createNewAccount($name, $surname, $email, $privacy, $sendCredentials = false, $community = null)
    {
        return UserProfileUtility::createNewAccount($name, $surname, $email, $privacy, $sendCredentials, $community);
    }

}
