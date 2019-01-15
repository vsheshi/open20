<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti\utility
 * @category   CategoryName
 */

namespace lispa\amos\documenti\utility;

use lispa\amos\core\icons\AmosIcons;
use lispa\amos\documenti\models\Documenti;
use yii\base\Object;

/**
 * Class DocumentsUtility
 * @package lispa\amos\documenti\utility
 */
class DocumentsUtility extends Object
{
    /**
     * @param Documenti $model
     * @param bool $onlyIconName If true return only the icon name ready to use in AmosIcon::show method.
     * @return string
     */
    public static function getDocumentIcon($model, $onlyIconName = false)
    {
        if ($model->is_folder) {
            $folderIconName = 'folder-open';
            if ($onlyIconName) {
                return $folderIconName;
            }
            return AmosIcons::show($folderIconName, ['class' => 'icon_widget_graph'], 'dash');
        }
        $iconName = 'file-o';
        $documentFile = $model->getDocumentMainFile();
        if (!is_null($documentFile)) {
            $docExtension = strtolower($documentFile->type);
            switch ($docExtension) {
                case 'doc':
                    //$iconName = 'file-word-o';
                    $iconName = 'file-doc-o';
                    break;
                case 'docx':
//                    $iconName = 'file-word-o';
                    $iconName = 'file-docx-o';
                    break;
                case 'rtf':
//                    $iconName = 'file-o';
                    $iconName = 'file-rtf-o';
                    break;
                case 'xls':
//                    $iconName = 'file-excel-o';
                    $iconName = 'file-xls-o';
                    break;
                case 'xlsx':
//                    $iconName = 'file-excel-o';
                    $iconName = 'file-xlsx-o';
                    break;
                case 'txt':
//                    $iconName = 'file-text-o';
                    $iconName = 'file-txt-o';
                    break;
                case 'pdf':
//                    $iconName = 'file-pdf-o';
                    $iconName = 'file-pdf-o2';
                    break;
                default:
                    $iconName = 'file-o';
                    break;
            }
        } 
        if ($onlyIconName) {
            return $iconName;
        }
        return AmosIcons::show($iconName, ['class' => 'icon_widget_graph'], 'dash');
    }
}
