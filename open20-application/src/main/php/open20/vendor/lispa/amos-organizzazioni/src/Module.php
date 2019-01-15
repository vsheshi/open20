<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni;

use lispa\amos\core\interfaces\OrganizationsModuleInterface;
use lispa\amos\core\module\AmosModule;
use lispa\amos\organizzazioni\models\Profilo;
use lispa\amos\organizzazioni\models\ProfiloUserMm;
use lispa\amos\organizzazioni\widgets\JoinedOrganizationsWidget;
use lispa\amos\organizzazioni\widgets\JoinedOrgParticipantsTasksWidget;
use lispa\amos\organizzazioni\widgets\ProfiloCardWidget;
use yii\base\Exception;

/**
 * organizzazioni module definition class
 */
class Module extends AmosModule implements OrganizationsModuleInterface
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\organizzazioni\controllers';
    public $newFileMode = 0666;
    public $name = 'organizzazioni';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

		\Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/controllers/', __DIR__ . '/controllers/');
        // custom initialization code goes here
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));

    }

    protected function getDefaultModels()
    {
        return [
            'Profilo' => __NAMESPACE__ . '\\' . 'models\Profilo',
        ];
    }

    /**
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'organizzazioni';
    }

    public function getWidgetGraphics()
    {
        return NULL;
    }

    public function getWidgetIcons()
    {
        return [
            \lispa\amos\organizzazioni\widgets\icons\WidgetIconProfilo::className(),
        ];
    }

    /**
     * @return array|string
     */
    public function getOrganizationModelClass()
    {
        return $this->model('Profilo');
    }

    /**
     * @return string
     */
    public function getOrganizationCardWidgetClass()
    {
        return ProfiloCardWidget::className();
    }

    /**
     * @return string
     */
    public function getAssociateOrgsToProjectWidgetClass(){
        return JoinedOrganizationsWidget::className();
    }


    /**
     * @return string
     */
    public function getAssociateOrgsToProjectTaskWidgetClass(){
        return JoinedOrgParticipantsTasksWidget::className();
    }

    /**
     * @return mixed
     */
    public function getOrganizationsListQuery()
    {
        $query = Profilo::find();
        $query->orderBy(['name' => SORT_ASC]);
        return $query;
    }


    /**
     * @param $user_id
     * @param $organization_id
     */
    public function saveOrganizationUserMm($user_id, $organization_id)
    {
        try {
            $org = ProfiloUserMm::findOne(['profilo_id' => $organization_id, 'user_id' => $user_id]);
            if (empty($org)) {
                $org = new CompaniesEmployeesMm();
                $org->company_id = $organization_id;
                $org->user_id = $user_id;
                $org->save();
            }
        }catch(Exception $ex){
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }


    /**
     * @param $id
     */
    public function getOrganization($id)
    {
        $model = null;

        try {
            $model = Profilo::findOne(['id' => $id]);
        }catch (Exception $ex){
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $model;
    }
}
