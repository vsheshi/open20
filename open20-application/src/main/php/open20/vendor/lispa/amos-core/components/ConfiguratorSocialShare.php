<?php
/**
 */

namespace lispa\amos\core\components;

use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use ymaker\social\share\configurators\Configurator;


/**
 * Configurator for social network drivers.
 *
 * @since 1.0
 */
class ConfiguratorSocialShare extends Configurator
{
    /**
     * Configuration of social network drivers.
     *
     * @var array
     */
    public $socialNetworks = [];
    /**
     * CSS options for share links.
     *
     * @var array
     */

    public $options = [
        'class' => 'social-network',
        'style' => 'font-size: 23px; padding-left:2px;'
    ];

    public $registerMetaTags = false;

    /**
     * Set default values for special link options.
     */
    public function init()
    {
        $this->initDeafaultOption();
        parent::init();

    }

    public function initDeafaultOption(){
        if(empty($this->socialNetworks)) {
            $this->socialNetworks = [
                'facebook' => [
                    'class' => \lispa\amos\core\forms\editors\socialShareWidget\drivers\Facebook::class,
                    'label' => \yii\helpers\Html::tag('span', '', ['class' => 'am am-facebook-box']),
                ],
                'twitter' => [
                    'class' => \ymaker\social\share\drivers\Twitter::class,
                    'label' => \yii\helpers\Html::tag('span', '', ['class' => 'am am-twitter-box']),
                    'options' => ['class' => 'tw'],
                ],
                'googlePlus' => [
                    'class' => \ymaker\social\share\drivers\GooglePlus::class,
                    'label' => \yii\helpers\Html::tag('span', '', ['class' => 'am am-google-plus-box']),
                    'options' => ['class' => 'gp'],
                ],
                'linkedIn' => [
                    'class' => \ymaker\social\share\drivers\LinkedIn::class,
                    'label' => \yii\helpers\Html::tag('span', '', ['class' => 'am am-linkedin-box']),
                    'options' => ['class' => 'gp'],
                ],
                'email' => [
                    'class' => \lispa\amos\core\forms\editors\socialShareWidget\drivers\Email::class,
                    'label' => \yii\helpers\Html::tag('span', '', ['class' => 'am am-email']),
                    'options' => ['class' => 'gp'],
                ],
            ];
        }

    }
}
