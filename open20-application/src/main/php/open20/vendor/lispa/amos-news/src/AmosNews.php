<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\news
 * @category   CategoryName
 */

namespace lispa\amos\news;

use lispa\amos\core\module\AmosModule;
use lispa\amos\core\module\ModuleInterface;
use lispa\amos\news\widgets\graphics\WidgetGraphicsUltimeNews;
use lispa\amos\news\widgets\icons\WidgetIconAllNews;
use lispa\amos\news\widgets\icons\WidgetIconNews;
use lispa\amos\news\widgets\icons\WidgetIconNewsCategorie;
use lispa\amos\news\widgets\icons\WidgetIconNewsCreatedBy;
use lispa\amos\news\widgets\icons\WidgetIconNewsDashboard;
use lispa\amos\news\widgets\icons\WidgetIconNewsDaValidare;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class AmosNews
 * @package lispa\amos\news
 */
class AmosNews extends AmosModule implements ModuleInterface
{
    public static $CONFIG_FOLDER = 'config';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    /**
     * @var string $name
     */
    public $name = 'Notizie';

    /**
     * If this attribute is true the validation of the publication date is active
     * @var boolean $validatePublicationDate
     */
    public $validatePublicationDate = true;

    /**
     * @var bool|false $filterCategoriesByRole - if true, enables category role check via table news_category_roles_mm
     */
    public $filterCategoriesByRole = false;

    /**
     * @var bool|false $hidePubblicationDate
     */
    public $hidePubblicationDate = false;

    /**
     * Hide the Option wheel in the graphic widget
     * @var bool|false $hideWidgetGraphicsActions
     */
    public $hideWidgetGraphicsActions = false;

    /**
     * @var array $newsRequiredFields - mandatory fields in News form
     */
    public $newsRequiredFields = [
        'news_categorie_id',
        'titolo',
        'status'        
    ];

    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return "news";
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::setAlias('@lispa/amos/'.static::getModuleName().'/controllers', __DIR__.'/controllers');

        //Configuration: merge default module configurations loaded from config.php with module configurations set by the application
        $config = require(__DIR__.DIRECTORY_SEPARATOR.self::$CONFIG_FOLDER.DIRECTORY_SEPARATOR.'config.php');
        \Yii::configure($this, ArrayHelper::merge($config, $this));
    }

    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [
            WidgetIconNews::className(),
            WidgetIconNewsCategorie::className(),
            WidgetIconNewsCreatedBy::className(),
            WidgetIconNewsDaValidare::className(),
            WidgetIconNewsDashboard::className(),
            WidgetIconAllNews::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return [
            WidgetGraphicsUltimeNews::className(),
        ];
    }

    /**
     * Get default model classes
     */
    protected function getDefaultModels()
    {
        return [
            'News' => __NAMESPACE__.'\\'.'models\News',
            'NewsCategorie' => __NAMESPACE__.'\\'.'models\NewsCategorie',
        ];
    }

    /**
     * This method return the session key that must be used to add in session
     * the url from the user have started the content creation.
     * @return string
     */
    public static function beginCreateNewSessionKey()
    {
        return 'beginCreateNewUrl_'.self::getModuleName();
    }
}