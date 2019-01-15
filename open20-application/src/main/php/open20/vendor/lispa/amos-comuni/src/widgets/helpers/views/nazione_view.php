<div class="<?= isset($nazioneConfig['class']) ? $nazioneConfig['class'] : 'col-xs-3';?>">

<?php

    //id del campo: se specificato nelle option uso quello, altrimenti sarà nel formato 'campo_db-id'
    $id = isset($nazioneConfig['options']['id']) ? $nazioneConfig['options']['id'] : $nazioneConfig['attribute'].'-id';
    $provincia_id = isset($provinciaConfig['options']['id']) ? $provinciaConfig['options']['id'] : $provinciaConfig['attribute'].'-id';
    $comune_id = isset($comuneConfig['options']['id']) ? $comuneConfig['options']['id'] : $comuneConfig['attribute'].'-id';
    $cap_id = isset($capConfig['options']['id']) ? $capConfig['options']['id'] : $capConfig['attribute'].'-id';

    //nazione italia attiva la provincia
    echo $form->field($model, $nazioneConfig['attribute'])->widget(\kartik\select2\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\lispa\amos\comuni\models\IstatNazioni::find()->orderBy('nome')->asArray()->all(), 'id', 'nome'),
        'options' => array_merge(
            [
                'placeholder' => Yii::t('app', 'Digita il nome della nazione'),
                'id' => $id,
            ], !empty($nazioneConfig['options']) ? $nazioneConfig['options'] : []
        ),
        'pluginOptions' => array_merge(
            [
                'allowClear' => true
            ], !empty($nazioneConfig['pluginOptions']) ? $nazioneConfig['pluginOptions'] : []
        ),
    ]);

$script = <<< JS

    setTimeout( function(){
        cleanSelectByNazione( $("#{$id}").val(), "{$provincia_id}", "{$comune_id}", "{$cap_id}"  );
        
        $("#{$id}").on('change', function(){
            cleanSelectByNazione( $(this).val(), "{$provincia_id}", "{$comune_id}", "{$cap_id}"  );
        });
        
    }, 150);

JS;

$this->registerJs($script);
?>
</div>