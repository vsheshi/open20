<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\events
 * @category   CategoryName
 */

namespace lispa\amos\events;

use lispa\amos\core\module\AmosModule;
use lispa\amos\core\module\ModuleInterface;

/**
 * Class AmosEvents
 * @package lispa\amos\events
 */
class AmosEvents extends AmosModule implements ModuleInterface
{
    public static $CONFIG_FOLDER = 'config';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\events\controllers';

    public $newFileMode = 0666;
    public $name = 'Events';

    /**
     * If this attribute is true the validation of the publication date is active
     * @var boolean
     */
    public $validatePublicationDateEnd = true;
    /**
     * @var bool|false $enableGoogleMap
     */
    public $enableGoogleMap = true;
    /**
     * @var bool|false $hidePubblicationDate
     */
    public $enableInvitationManagement = true;
    /**
     * @var bool|false $hidePubblicationDate
     */
    public $hidePubblicationDate = false;

    /**
     * This param enable or disable the export button in lists.
     * @var bool $enableExport
     */
    public $enableExport = true;

    /**
     * @var bool $eventLengthRequired If true enable the required validator on field "length"
     */
    public $eventLengthRequired = false;

    /**
     * @var bool $eventMURequired If true enable the required validator on length measurement unit
     */
    public $eventMURequired = false;

    /**
     * @return string
     */
    public static function getModuleName()
    {
        return 'events';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/controllers', __DIR__ . '/controllers/');
        // custom initialization code goes here
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));
    }

    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return NULL;
    }

    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [
            \lispa\amos\events\widgets\icons\WidgetIconEvents::className(),
            \lispa\amos\events\widgets\icons\WidgetIconEventTypes::className(),
            \lispa\amos\events\widgets\icons\WidgetIconEventsCreatedBy::className(),
            \lispa\amos\events\widgets\icons\WidgetIconEventsToPublish::className(),
            \lispa\amos\events\widgets\icons\WidgetIconEventsManagement::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultModels()
    {
        return [];
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
