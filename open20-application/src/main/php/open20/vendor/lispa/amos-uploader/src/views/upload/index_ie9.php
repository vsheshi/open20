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

$this->title = Module::t('uploader', 'Carica File');
$this->params['breadcrumbs'][] = $this->title;


$doneMessage = Module::t('uploader', 'Upload completed');
$module = Module::getInstance();
$serverUpload = $module->uploaderServer;
$allowedFileExtensions = $module->allowedFileExtensions;

$callbackUrl = \Yii::$app->request->get('callbackUrl');

$js = <<<JS
//Let go to this url with uploaded files
var callbackUrl = "{$callbackUrl}";

$('#upload-btn').on('click', function (){
    // $('#upload-input').click();
    $(this).prop('disabled',true);
    $('.progress-bar').text('0%');
    $('.progress-bar').width('0%');
});
    function successFunction() {
        
          if(callbackUrl) {
              $('.progress').text('Caricamento completato, attendere decompressione file ...');
              var a = $('#my_iframe').contents().find('body').html();
              console.log(typeof a);
                   var dat = JSON.parse(a);

                    window.location.href = callbackUrl + "?" + jQuery.param( dat );
                }
        // alert($('#my_iframe').contents().find('body').html());
        
    }
$('#upload-btn').on('click', function(){

     var files = $('#upload-input').get(0).files;

        document.getElementById('my_form').target = 'my_iframe'; //'my_iframe' is the name of the iframe
        var callback = function () {
                successFunction();
            $('#my_iframe').unbind('load', callback);
            };
        
        $('#my_iframe').bind('load', callback);
        $('#hfParam').val('id:1');

        $('.progress').show();
        $('#my_form').submit();
        //$("#my_form").trigger("submit");


  
});

JS;

$this->registerJs($js);


?>

<div class="<?= Yii::$app->controller->id ?>-form col-xs-12 nop m-t-20">

    <div class="row">
        <form class = "default-form" id="my_form" action="<?=$serverUpload?>" method="post" enctype = "multipart/form-data">
            <div class="form-group">
                <label for="fileUpload">File upload</label>
                <input name="my_hidden" id="hfParam" type="hidden" />
                <input class="form-control" type="file" id="upload-input" name="uploads[]"  />
                <iframe id='my_iframe' name='my_iframe' src="" style="display:none">
                </iframe>
            </div>
        </form>
    </div>
    <div class="row m-t-15">
        <div class="col-xs-12">
            <div class="progress" hidden>
                <?= Module::t('uploader', 'Caricamento file in corso ...') ?>
            </div>
            <div class="bk-btnFormContainer">
                <button id="upload-btn" class="btn btn-primary" type="button"><?= Module::t('uploader',
                        'Start Upload') ?></button>
            </div>
        </div>
    </div>
</div>
