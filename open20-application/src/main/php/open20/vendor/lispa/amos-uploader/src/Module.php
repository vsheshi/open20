<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\uploader
 * @category   CategoryName
 */

namespace lispa\amos\uploader;

use lispa\amos\core\module\AmosModule;
use lispa\amos\core\module\ModuleInterface;
use lispa\amos\uploader\widgets\icons\WidgetIconUploader;


class Module extends AmosModule implements ModuleInterface
{
    public static $CONFIG_FOLDER = 'config';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    /**
     *  The node JS server used by uploader
     * 'uploaderServer' => 'http://<host>:<port>/<upload action name in node.js script>',
     *  eg. http://www.devel.lispa.it:8000/upload
     *
     * @var string $uploaderServer
     */
    public $uploaderServer;

    /**
     * @var array $allowedFileExtensions
     */
    public $allowedFileExtensions = ['zip'];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\uploader\controllers';

    public $newFileMode = 0666;

    public $name = 'Uploader';

    /**
     * @return string
     */
    public static function getModuleName()
    {
        return 'uploader';
    }

    /**
     * @inheritdoc
     */
    public function getAmosUniqueId()
    {
        return 'uploader';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/controllers', __DIR__ . '/controllers');
    }

    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [
            WidgetIconUploader::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultModels()
    {
        return [

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