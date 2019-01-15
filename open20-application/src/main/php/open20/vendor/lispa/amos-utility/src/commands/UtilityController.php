<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos-basic-template
 * @category   CategoryName
 */

namespace lispa\amos\utility\commands;

use lispa\amos\core\utilities\ClassUtility;
use yii\console\Controller;
use yii\helpers\Console;


class UtilityController extends Controller
{

    public $moduleName = null;

    public function options($actionID){
        return ['moduleName'];
    }


    /**
     *
     */
    public /**void*/function actionResetDashboardByModule(/**void*/){
        $classname = 'lispa\amos\dashboard\utility\DashboardUtility';

        try {
            if (ClassUtility::classExist($classname)) {
                if(!empty($this->moduleName)) {
                    Console::stdout('Reset dashboard for:'.$this->moduleName);
                    $classname::resetDashboardsByModule($this->moduleName);
                }else{
                    Console::stdout('Missing moduleName param.');
                }
            }
        }catch (\yii\base\Exception $ex){

        }
    }
}