<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    pcd20\import\widgets\icons
 * @category   CategoryName
 */

namespace pcd20\import\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\uploader\Module;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconUploader
 * @package pcd20\import\widgets\icons
 */
class WidgetIconImport extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $isEdge = false;
        if(preg_match("/Edge/i", $_SERVER['HTTP_USER_AGENT'], $output_array)){
            $isEdge = true;
        }

        if(preg_match("/Explorer/i", $_SERVER['HTTP_USER_AGENT'], $output_array)){
            $isEdge = true;
        }

        $url = '/dashboard';
        if(!$isEdge) {
            $url = ['/uploader/upload/index', 'callbackUrl' => '/import/default/import'];
        }
        else {
            $js = <<<JS
            $('.alert-import').click(function(event){
                event.preventDefault();
                alert("L'importazione delle aree di lavoro è supportata completamente utilizzando il web browser Chrome; perciò per eseguirla è necessario usare questo browser: apri Chrome, accedi alla piattaforma ed esegui di nuovo questa funzione.");
            });
JS;
            $controller = \Yii::$app->controller;
            $view = $controller->getView();
            $view->registerJs($js);
        }

        $this->setLabel(Module::tHtml('pcd20import', 'Import Workspace'));
        $this->setDescription(Module::t('pcd20import', 'Upload files of great size'));
        $this->setIcon('linentita');
        $this->setUrl($url);
        $this->setCode('UPLOADER');
        $this->setModuleName('uploader');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey',
            'alert-import'
        ]));
    }

}
