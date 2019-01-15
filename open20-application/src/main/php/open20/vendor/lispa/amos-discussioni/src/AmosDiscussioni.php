<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

namespace lispa\amos\discussioni;

use lispa\amos\core\module\AmosModule;
use lispa\amos\core\module\ModuleInterface;
use lispa\amos\discussioni\widgets\graphics\WidgetGraphicsDiscussioniInEvidenza;
use lispa\amos\discussioni\widgets\graphics\WidgetGraphicsUltimeDiscussioni;
use lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopic;
use lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopicCreatedBy;
use lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniTopicDaValidare;
use Yii;
use yii\console\Application;

/**
 * Class AmosDiscussioni
 * @package lispa\amos\discussioni
 */
class AmosDiscussioni extends AmosModule implements ModuleInterface
{
    /**
     * @var string $controllerNamespace the controller namespace
     */
    public $controllerNamespace = 'lispa\amos\discussioni\controllers';
    public $geolocalEnabled = false;
    public $geolocalLatColumn = 'lat';
    public $geolocalLngColumn = 'lng';
    public $geolocalRadius = '5000';
    public $name = 'Discussioni';

    /**
     * @var bool $hideWidgetGraphicsActions
     */
    public $hideWidgetGraphicsActions = false;
    
    /**
     * @return string - The name of the module
     */
    public static function getModuleName()
    {
        return "discussioni";
    }
    
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof Application) {
            $this->controllerNamespace = 'lispa\amos\discussioni\console\controllers';
            \Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/console/controllers', __DIR__ . '/console/controllers');
            \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'console' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));
        }
    }
    
    /**
     *
     */
    public function init()
    {
        parent::init();
        
        if (\Yii::$app instanceof Application) {
            \Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/controllers', __DIR__ . '/controllers/');
            //aggiunge le configurazioni trovate nel file config/config.php
            Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));
        } else {
            \Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/controllers', __DIR__ . '/controllers/');
            //aggiunge le configurazioni trovate nel file config/config.php
            Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));
        }
    }
    
    /**
     * @return boolean
     */
    public function isGeolocalEnabled()
    {
        return $this->geolocalEnabled;
    }
    
    /**
     * @param boolean $geolocalEnabled
     */
    public function setGeolocalEnabled($geolocalEnabled)
    {
        $this->geolocalEnabled = $geolocalEnabled;
    }
    
    /**
     * @return string
     */
    public function getGeolocalLatColumn()
    {
        return $this->geolocalLatColumn;
    }
    
    /**
     * @param string $geolocalLatColumn
     */
    public function setGeolocalLatColumn($geolocalLatColumn)
    {
        $this->geolocalLatColumn = $geolocalLatColumn;
    }
    
    /**
     * @return string
     */
    public function getGeolocalLngColumn()
    {
        return $this->geolocalLngColumn;
    }
    
    /**
     * @param string $geolocalLngColumn
     */
    public function setGeolocalLngColumn($geolocalLngColumn)
    {
        $this->geolocalLngColumn = $geolocalLngColumn;
    }
    
    /**
     * @return string
     */
    public function getGeolocalRadius()
    {
        return $this->geolocalRadius;
    }
    
    /**
     * @param string $geolocalRadius
     */
    public function setGeolocalRadius($geolocalRadius)
    {
        $this->geolocalRadius = $geolocalRadius;
    }
    
    /**
     * @return array: classname of the graphic widgets
     */
    public function getWidgetGraphics()
    {
        return [
            WidgetGraphicsUltimeDiscussioni::className(),
            WidgetGraphicsDiscussioniInEvidenza::className(),
        ];
    }
    
    /**
     * @return array: classname of the icon widgets
     */
    public function getWidgetIcons()
    {
        return [
            WidgetIconDiscussioniTopic::className(),
            WidgetIconDiscussioniTopicCreatedBy::className(),
            WidgetIconDiscussioniTopicDaValidare::className(),
        ];
    }
    
    /**
     * @return array
     */
    protected function getDefaultModels()
    {
        return [
            'DiscussioniTopic' => __NAMESPACE__ . '\\' . 'models\DiscussioniTopic',
            'DiscussioniCommenti' => __NAMESPACE__ . '\\' . 'models\DiscussioniCommenti',
            'DiscussioniRisposte' => __NAMESPACE__ . '\\' . 'models\DiscussioniRisposte',
        ];
    }
    
    /**
     * This method return the session key that must be used to add in session
     * the url from the user have started the content creation.
     * @return string
     */
    public static function beginCreateNewSessionKey()
    {
        return 'beginCreateNewUrl_' . self::getModuleName();
    }
}
