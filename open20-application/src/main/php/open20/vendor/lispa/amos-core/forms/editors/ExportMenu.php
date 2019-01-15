<?php


namespace lispa\amos\core\forms\editors;
use lispa\amos\core\forms\editors\assets\ExportMenuAsset;
use kartik\dialog\Dialog;
use kartik\export\ExportColumnAsset;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;


/**
 * Export menu widget. Export tabular data to various formats using the `\PhpOffice\PhpSpreadsheet\Spreadsheet library
 * by reading data from a dataProvider - with configuration very similar to a GridView.
 *
 * @since  1.0
 */
class ExportMenu extends \kartik\export\ExportMenu
{
    /**
     * @var string the view file for rendering the export form
     */
    public $exportFormView = '@vendor/kartik-v/yii2-export/views/_form';

    /**
     * @var string the view file for rendering the columns selection
     */
    public $exportColumnsView = '@vendor/kartik-v/yii2-export/views/_columns';

    public $afterSaveView = '@vendor/kartik-v/yii2-export/views/_view';

    /**
     * Registers client assets needed for Export Menu widget
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        Dialog::widget($this->krajeeDialogSettings);
        ExportMenuAsset::register($view);
        $this->messages += [
            'allowPopups' => \Yii::t(
                'kvexport',
                'Disable any popup blockers in your browser to ensure proper download.'
            ),
            'confirmDownload' => Yii::t('kvexport', 'Ok to proceed?'),
            'downloadProgress' => Yii::t('kvexport', 'Generating the export file. Please wait...'),
            'downloadComplete' => Yii::t(
        'kvexport',
        'Request submitted! You may safely close this dialog after saving your downloaded file.'
    ),
        ];
        $formId = $this->exportFormOptions['id'];
        $options = Json::encode(
            [
                'formId' => $formId,
                'messages' => $this->messages,
                'dialogLib' => new JsExpression(
                    ArrayHelper::getValue($this->krajeeDialogSettings, 'libName', 'krajeeDialog')
                ),
            ]
        );
        $menu = 'kvexpmenu_' . hash('crc32', $options);
        $view->registerJs("var {$menu} = {$options};\n", View::POS_HEAD);
        $script = '';
        foreach ($this->exportConfig as $format => $setting) {
            if (!isset($setting) || $setting === false) {
                continue;
            }
            $id = $this->options['id'] . '-' . strtolower($format);
            $options = [
                'settings' => new JsExpression($menu),
                'alertMsg' => $setting['alertMsg'],
                'target' => $this->target,
                'showConfirmAlert' => $this->showConfirmAlert,
            ];
            if ($this->_columnSelectorEnabled) {
                $options['columnSelectorId'] = $this->columnSelectorOptions['id'];
            }
            $options = Json::encode($options);
            $script .= "jQuery('#{$id}').exportdata({$options});\n";
        }
        if ($this->_columnSelectorEnabled) {
            $id = $this->columnSelectorMenuOptions['id'];
            ExportColumnAsset::register($view);
            $script .= "jQuery('#{$id}').exportcolumns({});\n";
        }
        if (!empty($script) && isset($this->pjaxContainerId)) {
            $script .= "jQuery('#{$this->pjaxContainerId}').on('pjax:complete', function() {
                {$script}
            });\n";
        }
        $view->registerJs($script);
    }
}
