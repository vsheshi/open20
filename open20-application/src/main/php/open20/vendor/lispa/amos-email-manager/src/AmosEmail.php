<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */

namespace lispa\amos\emailmanager;

use lispa\amos\core\module\AmosModule;
use lispa\amos\emailmanager\base\AmosEmailManager;
use yii\log\Logger;
use Yii;
use yii\base\Exception;

class AmosEmail extends AmosModule implements \yii\base\BootstrapInterface
{

    public static $moduleId = 'email';
    public $name = 'Email manager';

    /**
     * @var string Template type, can be "db" or "php".
     */
    public $templateType = "php";
    public $templatePath = "/emails/";

    /**
     * @var string The default layout to use for template emails.
     */
    public $defaultTemplate = "layout_default";
    public $defaultLayout = "layout_fancy";
    public $controllerNamespace = 'lispa\amos\emailmanager\controllers';
    private $emailManager = null;

    
    /**
     * 
     * @return string
     */
    public static function getModuleName()
    {
        return self::$moduleId;
    }

    /**
     * 
     */
    public function init()
    {
        parent::init();
        \Yii::setAlias('@email', __DIR__ . '/');
        \Yii::setAlias('@lispa/amos/emailmanager/commands', __DIR__ . '/commands/');
        // initialize the module with the configuration loaded from config.php
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));
        if (Yii::$app instanceof \yii\web\Application)
        {
            $this->controllerMap = array(
               'spool' => 'lispa\amos\emailmanager\controllers\EmailSpoolController',
               'template' => 'lispa\amos\emailmanager\controllers\EmailTemplateController',
            );
        }
        $this->initializeEmailManager();
    }

    /**
     * 
     */
    private function initializeEmailManager()
    {
        $this->emailManager = new AmosEmailManager();
        $this->emailManager->defaultLayout = $this->defaultLayout;
        $this->emailManager->defaultTemplate = $this->defaultTemplate;
        $this->emailManager->templateType = $this->templateType;
        $this->emailManager->templatePath = $this->templatePath;
    }

    /**
     * 
     * @param \yii\console\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application)
        {
            $this->controllerNamespace = 'lispa\amos\emailmanager\commands';
        }
    }

    /**
     * 
     */
    protected function getDefaultModels()
    {
        return [
            'EmailSpool' => __NAMESPACE__ . '\\' . 'models\EmailSpool',
            'EmailTemplate' => __NAMESPACE__ . '\\' . 'models\EmailTemplate',
        ];
    }

    /**
     * 
     * @see
     */
    public function getWidgetGraphics()
    {
        return [];
    }

    /**
     * 
     * @see
     */
    public function getWidgetIcons()
    {
        return [];
    }

    /**
     * Sends email message queue using default transport
     *
     * @param string $from format accepted:
     *   
     *   1) 'example@example.com'
     *   2) 'example@example.com alias' the method considers the email address up to the first space, everything that follows is considered alias. 
     *   
     * @param string $to
     * @param string $subject
     * @param string $text
     * @param integer $priority
     * @param array $files
     * @param array $bcc
     */
    public function queue($from, $to, $subject, $text, $files = [], $bcc = [], $params = [],$priority = 0)
    {
        $retValue = false;
        try
        {
            if ($this->emailManager)
            {
                $this->emailManager->defaultLayout = $this->defaultLayout;
                $this->emailManager->defaultTemplate = $this->defaultTemplate;
                $retValue = $this->emailManager->queue($from, $to, $subject, $text, $files, $bcc, $params,$priority);
            }
        } catch (Exception $ex)
        {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $retValue;
    }

    /**
     * Sends email message immediately using default transport
     *
     * @param string $from format accepted:
     *   
     *   1) 'example@example.com'
     *   2) 'example@example.com alias' the method considers the email address up to the first space, everything that follows is considered alias. 
     *   
     * @param string $to
     * @param string $subject
     * @param string $text
     * @param array $files
     * @param array $bcc
     * @return bool
     */
    public function send($from, $to, $subject, $text, $files = [], $bcc = [], $params = [], $save_in_queue = true)
    {
        $retValue = false;
        try
        {
            if ($this->emailManager)
            {
                $this->emailManager->defaultLayout = $this->defaultLayout;
                $this->emailManager->defaultTemplate = $this->defaultTemplate;
                $retValue = $this->emailManager->send($from, $to, $subject, $text, $files, $bcc,$params,$save_in_queue);
            }
        } catch (Exception $ex)
        {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $retValue;
    }

    /**
     * 
     * @param integer $loopLimit
     * @return integer
     */
    public function spool($loopLimit = 1000)
    {
        $retValue = 0;
        try
        {
            if ($this->emailManager)
            {
                $retValue = $this->emailManager->spool($loopLimit);
            }
        } catch (Exception $ex)
        {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $retValue;
    }

}
