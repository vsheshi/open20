<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\attachments
 * @category   CategoryName
 */

namespace lispa\amos\attachments\components;

use kartik\widgets\FileInput;
use lispa\amos\attachments\FileModule;
use lispa\amos\attachments\FileModuleTrait;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\jui\JuiAsset;

/**
 * Class AttachmentsInput
 * @package File\components
 * @property FileActiveRecord $model
 */
class AttachmentsInput extends FileInput
{
    use FileModuleTrait;

    public $attribute; // TODO verificarne la reale utilità

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        JuiAsset::register($this->view);

        if (empty($this->model)) {
            throw new InvalidConfigException(FileModule::t('amosattachments', "Property {model} cannot be blank"));
        }

        FileHelper::removeDirectory($this->getModule()->getUserDirPath($this->attribute)); // Delete all uploaded files in past

        $initials = $this->model->isNewRecord ? [] : $this->model->getInitialPreviewByAttributeName($this->attribute,'original');
        $initialCount = count($initials);

        $fileValidatorForAttribute = $this->model->getFileValidator($this->attribute);

        $this->pluginOptions = array_replace(
            [
                //'uploadUrl' => Url::toRoute(['/file/file/upload', 'attribute' => $this->attribute, 'model' => get_class($this->model)]),
                'initialPreview' => $initials,
                'initialPreviewConfig' => $this->model->isNewRecord ? [] : $this->model->getInitialPreviewConfigByAttributeName($this->attribute),
                'uploadAsync' => false,
                //'otherActionButtons' => '<button class="download-file btn-xs btn-default" title="download" {dataKey}><i class="glyphicon glyphicon-download"></i></button>',
                'overwriteInitial' => false,
                'initialPreviewCount' => $initialCount,
                'validateInitialCount' => true,
                'maxFileCount' => $fileValidatorForAttribute ? $fileValidatorForAttribute->maxFiles : 1,
                'showPreview' => true,
                'showCaption' => true,
                'showRemove' => true,
                'showDrag' => false,
                'indicatorNew' => false,
                'previewSettings' => [
                    'image' => [
                        'width' => '100px',
                        'height' => '100px'
                    ]
                ],
                'allowedPreviewTypes' => false,
                'previewFileIconSettings' => [
                    'docx'=> '<span class="glyphicon glyphicon-file"></span>',
                    'jpg' => '<span class="glyphicon glyphicon-file"></span>',
                    'png' => '<span class="glyphicon glyphicon-file"></span>',
                    'gif' => '<span class="glyphicon glyphicon-file"></span>',
                 ],
                'showUpload' => false,
                'layoutTemplates' => [
                    'actions' => '<div class="file-actions"><div class="file-footer-buttons">{upload} {delete} {zoom} {other}</div><div class="clearfix"></div></div>'
                ],
                'msgSizeTooSmall' => !empty($this->options['multiple']) && $this->options['multiple'] ? FileModule::t('amosattachments', 'Uno o più file selezionati risultano essere vuoti, quindi non verranno caricati.') : FileModule::t('amosattachments', 'Il file selezionato risulta essere vuoto quindi non verrà caricato.'),
            ],
            $this->pluginOptions
        );

        if ($this->pluginOptions['showPreview'] == false) {
            $this->pluginOptions['elErrorContainer'] = '#errorDropUpload-' . $this->attribute;
            $this->pluginOptions['layoutTemplates']['main1'] = "<div id=\"errorDropUpload-{$this->attribute}\"></div>
                                                                {preview}
                                                                <div class=\"kv-upload-progress kv-hidden\"></div><div class=\"clearfix\"></div>
                                                                <div class=\"input-group {class}\">
                                                                  {caption}
                                                                  <div class=\"input-group-btn\">
                                                                    {remove}
                                                                    {cancel}
                                                                    {upload}
                                                                    {browse}
                                                                  </div>
                                                                </div>";

            $this->pluginOptions['layoutTemplates']['main2'] = "<div id=\"errorDropUpload -{$this->attribute}\"></div>
                                                                {preview}
                                                                <div class=\"kv-upload-progress hide\"></div>
                                                                {remove}{cancel}\n{upload}\n{browse}";
        }


        $fileAttribute = $this->pluginOptions['maxFileCount'] != 1 ? '[]' : '';

        $this->options = array_replace(
            $this->options,
            [
                //'id' => $this->id,
                'model' => $this->model,
                'attribute' => $this->attribute,
                'name' => $this->attribute . $fileAttribute,
                'multiple' => true
            ]
        );

        @parent::init();

        $inputId = $this->options['id'];

        $urlSetMain = Url::toRoute('/' . FileModule::getModuleName() . '/file/set-main');
        $confirmText = FileModule::t('amosattachments', "If you choose another file, the current file will be deleted");


$maxFiles = $this->pluginOptions['maxFileCount'];

        $js = <<<JS
            var fileInput{$this->attribute} = $('#{$inputId}');
            var files{$this->attribute} = fileInput{$this->attribute}.fileinput('getFilesCount');
            var form{$this->attribute} = fileInput{$this->attribute}.closest('form');
            var filesStack{$this->attribute} = fileInput{$this->attribute}.fileinput('getPreview');
            
            form{$this->attribute}.on('beforeValidate', function(event) {
                if (files{$this->attribute}) {
                    form{$this->attribute}.yiiActiveForm('remove', '{$inputId}');
                }
            });

	fileInput{$this->attribute}.on('click', function(event) {
                var imputBox = jQuery(this).parents('.file-input');
                var thumbs = jQuery('.file-preview .file-preview-thumbnails .file-preview-frame',imputBox);
                        
                if({$maxFiles} == 1 && thumbs.length >= 1) {
                    var r = confirm("{$confirmText}");
                    
                    console.log(filesStack{$this->attribute});
                    
                    if (r == false) {
                        event.preventDefault();
                    } else {
                        thumbs.each(function() {
                            var deleteButton = jQuery('.file-footer-buttons .kv-file-remove');
                            
                            deleteButton.trigger('click');
                        });
                    }
                }
            });
            
JS;
        \Yii::$app->view->registerJs($js);
    }
}

