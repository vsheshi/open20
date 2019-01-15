<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\uploader\views\upload
 * @category   CategoryName
 */


use lispa\amos\uploader\Module;
use kartik\file\FileInput;

\lispa\amos\layout\assets\SpinnerWaitAsset::register($this);
$this->title = Module::t('uploader', 'Carica File');
$this->params['breadcrumbs'][] = $this->title;

$doneMessage = Module::t('uploader', 'Upload completed');
$module = Module::getInstance();
$serverUpload = $module->uploaderServer;
$allowedFileExtensions = $module->allowedFileExtensions;

$callbackUrl = \Yii::$app->request->get('callbackUrl');
$alert = "<div id=\"w11\" class=\"alert-danger alert fade in\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>È obbligatorio caricare un file.</div>";
$isEdge  = $isEdge;
$js = <<<JS
//Let go to this url with uploaded files
var callbackUrl = "{$callbackUrl}";

$('#upload-btn').on('click', function (){
    // $('#upload-input').click();
    $(this).prop('disabled',true);
    $('.progress-bar').text('0%');
    $('.progress-bar').width('0%');
});

$('#upload-btn').on('click', function(){

    var files = $('#upload-input').get(0).files;

    if (files.length > 0){
        // create a FormData object which will be sent as the data payload in the
        // AJAX request
        var formData = new FormData();

        // loop through all the selected files and add them to the formData object
        for (var i = 0; i < files.length; i++) {
            var file = files[i];

            // add the files to formData object for the data payload
            formData.append('uploads[]', file, file.name);
        }
        if('$isEdge' == 'true'){
            $('.loading').show();
        }
        $.ajax({
            url: "$serverUpload",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            complete: function(xhr){
                $('#upload-btn').prop('disabled',false);
                
                var data = JSON.parse(xhr.responseText);
                
                if(callbackUrl) {
                    $('.loading').show();
                    debugger;
                    var callbackTo = new URL(callbackUrl,window.location.origin);
                    window.location.href = callbackTo.href + (callbackTo.href.indexOf('?')>=0 ? "&" : "?") + jQuery.param( data );
                }
            },
            xhr: function() {
                // create an XMLHttpRequest
                var xhr = new XMLHttpRequest();

                // listen to the 'progress' event
                xhr.upload.addEventListener('progress', function(evt) {

                    if (evt.lengthComputable) {
                        // calculate the percentage of upload completed
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);

                        // update the Bootstrap progress bar with the new percentage
                        $('.progress-bar').text(percentComplete + '%');
                        $('.progress-bar').width(percentComplete + '%');

                        // once the upload reaches 100%, set the progress bar text to done
                        if (percentComplete === 100) {
                            $('.progress-bar').html("$doneMessage");
                        }
                    }

                }, false);

                return xhr;
            }
        });

    }
    else {
        $(this).prop('disabled',false);
        $('#div-alert').append('$alert');
        return false;
    }
});
JS;

$this->registerJs($js);


?>
<div id="div-alert"></div>
<div class="loading" hidden></div>
<div class="<?= Yii::$app->controller->id ?>-form col-xs-12 nop m-t-20">
    <div class="col-xs-12 m-b-20">
        <?= \yii\helpers\Html::a('Archivio importazioni', ['/import/default/import-list'], ['class' => 'btn btn-navigation-primary']);?>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <?= FileInput::widget([
                'name' => 'File',
                'options' => [
                    'id' => 'upload-input',
                ],
                'pluginOptions' => [
                    'allowedFileExtensions' => $allowedFileExtensions,
                    'showUpload' => false,
                ]
            ]) ?>
        </div>
    </div>
    <div class="row m-t-15">
        <div class="col-xs-12">
            <div class="progress">
                <div class="progress-bar" role="progressbar"></div>
            </div>
            <div class="bk-btnFormContainer">
                <button id="upload-btn" class="btn btn-primary" type="button"><?= Module::t('uploader',
                        'Start Upload') ?></button>
            </div>
        </div>
    </div>
</div>
