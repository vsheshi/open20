<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\dashboard
 * @category   CategoryName
 */

namespace lispa\amos\dashboard\commands;

use lispa\amos\core\module\Module;
use lispa\amos\core\module\ModuleInterface;
use lispa\amos\dashboard\models\AmosWidgets;
use Yii;

class DashboardController extends \yii\console\Controller
{
    public function actionRefreshWidgets()
    {
        AmosWidgets::deleteAll();

        foreach (Yii::$app->getModules() as $id => $module) {
            $moduleObj = Yii::$app->getModule($id);
            $class = new \ReflectionClass($moduleObj);

            if ($id == 'news') {
                continue;
            }

            if ($class->implementsInterface(ModuleInterface::class)) {
                /**@var $moduleObj Module */
                if (($WidgetIcons = $moduleObj->getWidgetIcons())) {
                    foreach ($WidgetIcons as $WidgetIcon) {
                        $AmosWidget = new AmosWidgets(
                            [
                                'classname' => get_class(Yii::createObject($WidgetIcon)),
                                'type' => AmosWidgets::TYPE_ICON,
                                'status' => AmosWidgets::STATUS_ENABLED,
                                'module' => $moduleObj->getModuleName()
                            ]
                        );
                        $AmosWidget->save();
                    }
                }
                if (($WidgetGraphics = $moduleObj->getWidgetGraphics())) {
                    foreach ($WidgetGraphics as $WidgetGraphic) {
                        $AmosWidget = new AmosWidgets(
                            [
                                'classname' => get_class($WidgetGraphic),
                                'type' => AmosWidgets::TYPE_GRAPHIC,
                                'status' => AmosWidgets::STATUS_ENABLED,
                                'module' => $moduleObj->getModuleName()
                            ]
                        );
                        $AmosWidget->save();
                    }
                }
            }
        }
    }

}