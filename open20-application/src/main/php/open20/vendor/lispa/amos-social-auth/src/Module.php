<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\socialauth
 * @category   CategoryName
 */

namespace lispa\amos\socialauth;

use common\models\SocialIdmUser;
use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\chat\models\search\UserContactsQuery;
use lispa\amos\core\module\AmosModule;
use lispa\amos\core\user\User;
use yii\base\BootstrapInterface;

/**
 * Class Module
 * @package lispa\amos\socialauth
 */
class Module extends AmosModule implements BootstrapInterface
{
    public static $CONFIG_FOLDER = 'config';

    /**
     * @var array providers configuration
     */
    public $providers = [];

    /**
     * @var $enableLogin bool Is Login With Social Enabled?
     */
    public $enableLogin;

    /**
     * @var $enableLogin bool Is Social Account Link Enabled?
     */
    public $enableLink;

    /**
     * @var $enableLogin bool Is Social registration Enabled?
     */
    public $enableRegister;

    /**
     * @inheritdoc
     */
    static $name = 'amossocialauth';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\socialauth\controllers';

    public $timeout = 180;

    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        //Get Headers to ckeck the reverse proxy header datas
        $headers = \Yii::$app->request->getHeaders();

        //Get Session IDM datas (copy of headers)
        $sessionIDM = \Yii::$app->session->get('IDM');

        //Link to current user with IDM
        if ($headers->get('serialNumber')) {
            $this->tryIdmLink('header_idm', $headers);
        } else if ($headers->get('saml_attribute_codicefiscale')) {
            $this->tryIdmLink('header_spid', $headers);
        } else if ($sessionIDM && $sessionIDM['saml_attribute_serialnumber']) {
            $this->tryIdmLink('idm', $headers);
        } else if ($sessionIDM && $sessionIDM['saml_attribute_codicefiscale']) {
            $this->tryIdmLink('spid', $sessionIDM);
        }
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Module name
     * @return string
     */
    public static function getModuleName()
    {
        return self::$name;
    }

    public function getWidgetIcons()
    {
        return [
        ];
    }

    public function getWidgetGraphics()
    {
        return [
        ];
    }

    /**
     * Get default models
     * @return array
     */
    protected function getDefaultModels()
    {
        return [
        ];
    }

    /**
     * @param string $type
     * @param $dataFetch
     * @return bool
     */
    public function tryIdmLink($type = 'headers', $dataFetch)
    {
        //Default values
        $matricola = null;
        $nome = null;
        $cognome = null;
        $emailAddress = null;
        $codiceFiscale = null;

        //Based on type i pick the user identiffier
        switch ($type) {
            case 'idm':
                {
                    $matricola = $dataFetch['serialNumber'];
                    $nome = $dataFetch['name'];
                    $cognome = $dataFetch['familyName'];
                    $emailAddress = $dataFetch['email'];
                    $codiceFiscale = $dataFetch['codiceFiscale'];
                }
                break;
            case 'spid':
                {
                    $matricola = $dataFetch['saml_attribute_codicefiscale'];
                    $nome = $dataFetch['saml_attribute_nome'];
                    $cognome = $dataFetch['saml_attribute_cognome'];
                    $emailAddress = $dataFetch['saml_attribute_emailaddress'];
                    $codiceFiscale = $dataFetch['saml_attribute_codicefiscale'];
                }
                break;
            case 'header_idm':
                {
                    $matricola = $dataFetch->get('serialNumber');
                    $nome = $dataFetch->get('name');
                    $cognome = $dataFetch->get('familyName');
                    $emailAddress = $dataFetch->get('email');
                    $codiceFiscale = $dataFetch->get('fiscalCode');
                }
                break;
            case 'header_spid':
                {
                    $matricola = $dataFetch->get('saml_attribute_codicefiscale');
                    $nome = $dataFetch->get('saml_attribute_nome');
                    $cognome = $dataFetch->get('saml_attribute_cognome');
                    $emailAddress = $dataFetch->get('saml_attribute_emailaddress');
                    $codiceFiscale = $dataFetch->get('saml_attribute_codicefiscale');
                }
        }

        //Data to store in session in case header is not filled
        $sessionIDM = [
            'matricola' => $matricola,
            'nome' => $nome,
            'cognome' => $cognome,
            'emailAddress' => $emailAddress,
            'codiceFiscale' => $codiceFiscale
        ];

        //Store to session
        \Yii::$app->session->set('IDM', $sessionIDM);

        //Find for existing relation
        $relation = SocialIdmUser::findOne(['numeroMatricola' => $matricola]);

        //Get timeout for app login
        $loginTimeout = \Yii::$app->params['loginTimeout'] ?: 3600;

        if ($relation && $relation->id) {
            if (\Yii::$app->user->isGuest) {
                $signIn = \Yii::$app->user->login($relation->user, $loginTimeout);

                //Remove session data
                \Yii::$app->session->remove('IDM');

                return true;
            } else {
                return false;
            }
        } else {
            if (\Yii::$app->user->isGuest) {
                $existsByFC = UserProfile::findOne(['codice_fiscale' => $codiceFiscale]);
                $existsByEmail = User::findOne(['email' => $emailAddress]);
                $existsByName = UserProfile::findOne(['nome' => $nome, 'cognome' => $cognome]);

                //If the use does not exosts create e new one
                if (!$existsByFC && !$existsByEmail && !$existsByName) {
                    //Create a new account
                    $newUser = AmosAdmin::createNewAccount(
                        $nome,
                        $cognome,
                        $emailAddress,
                        true
                    );

                    //If $newUser is false the user is not created
                    if (!$newUser || isset($newUser['error'])) {
                        \Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Unable to register, user creation error'));

                        if ($newUser['messages']) {
                            foreach ($newUser['messages'] as $message) {
                                \Yii::$app->session->addFlash('danger', Module::t('amossocialauth', reset($message)));
                            }
                        }

                        return false;
                    }

                    //Get the newly created user
                    $userCreated = User::findOne(['id' => $newUser['user']]);

                    //Log in user
                    $signIn = \Yii::$app->user->login($userCreated, $loginTimeout);
                } else {
                    if($existsByFC) {
                        //Log in user
                        $signIn = \Yii::$app->user->login($existsByFC->user, $loginTimeout);
                    } else if($existsByEmail) {
                        //Log in user
                        $signIn = \Yii::$app->user->login($existsByEmail, $loginTimeout);
                    } else if($existsByName) {
                        //Log in user
                        $signIn = \Yii::$app->user->login($existsByName->user, $loginTimeout);
                    } else {
                        return false;
                    }
                }

                //Remove session data
                \Yii::$app->session->remove('IDM');
            }

            $newRelation = new SocialIdmUser();
            $newRelation->numeroMatricola = $matricola;
            $newRelation->nome = $nome;
            $newRelation->cognome = $cognome;
            $newRelation->emailAddress = $emailAddress;
            $newRelation->codiceFiscale = $codiceFiscale;
            $newRelation->user_id = \Yii::$app->user->id;
            $newRelation->save(false);

            return true;
        }

        return false;
    }
}
