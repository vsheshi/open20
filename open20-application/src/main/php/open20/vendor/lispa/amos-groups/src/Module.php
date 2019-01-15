<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\GROUPS
 * @category   CategoryName
 */

namespace lispa\amos\groups;

use lispa\amos\core\module\AmosModule;

class Module extends AmosModule
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\groups\controllers';
    public $newFileMode = 0666;
    public $name = 'groups';

    /** $className->method() has to return a dataProvider  of UserProfile */
    public $className;
    public $method;

    /** classname of the entity which you want to connect the group with
     *  if you want to connect with amos comunity is not needed
     */
    public $classNameParent;
    /** classname of a model that connect user to the parent entity
     * I you want to filter user to assign to grups by an userMm use thi 3 parameter*/
    public $classNameParentUserMm;
    public $parentAttributeMm;
    public $userAttributeMm;

    /** email address used for sending emails to group members  */
    public $email;
    public $layoutEmail;

    public $hiddenFields;

 // ------- EXAMPLE USING CONFIGURATION -----------
//    public $className = '\lispa\amos\groups\models\search\GroupsSearch';
//    public $method = 'searchProva';

//    public $classNameParent = '\backend\modules\enti\models\Enti';
//    public $classNameParentUserMm = '\backend\modules\enti\models\EntiUserMm';
//    public $parentAttributeMm = 'enti_id';
//    public $userAttributeMm = 'user_id';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));
    }

    protected function getDefaultModels()
    {
        return [];
    }

    /**
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'groups';
    }

    public function getWidgetGraphics()
    {
        return NULL;
    }

    public function getWidgetIcons()
    {
        return [

        ];
    }
}