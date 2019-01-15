<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comuni\widgets\helpers\views
 * @category   CategoryName
 */

use lispa\amos\comuni\AmosComuni;

//id del campo: se specificato nelle option uso quello, altrimenti sarà nel formato 'campo_db-id'
$comuneAttribute = $comuneConfig['attribute'];
$id = isset($comuneConfig['options']['id']) ? $comuneConfig['options']['id'] : $comuneAttribute.'-id';
$provinciaAttribute = $provinciaConfig['attribute'];
$id_provincia = isset($provinciaConfig['options']['id']) ? $provinciaConfig['options']['id'] : $provinciaAttribute.'-id';

?>

<div class="<?= isset($comuneConfig['class']) ? $comuneConfig['class'] : 'col-xs-3';?>">

<?= $form->field($model, $comuneAttribute)->widget( \kartik\depdrop\DepDrop::classname(), [
        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
        'data' => \yii\helpers\ArrayHelper::map(\lispa\amos\comuni\models\IstatComuni::find()->andWhere(['istat_province_id' => $model->$provinciaAttribute])->orderBy('nome')->asArray()->all(), 'id', 'nome'),
        'options' =>  array_merge(
            [
                'id' => $id,
            ], !empty($comuneConfig['options']) ? $comuneConfig['options'] : []
        ),
        'select2Options' => array_merge(
            [
                'pluginOptions' => ['allowClear' => true]
            ], !empty($comuneConfig['select2Options']) ? $comuneConfig['select2Options'] : []
        ),
        'pluginOptions' => array_merge(
            [
                'depends' => [$id_provincia],
                'placeholder' => AmosComuni::t('amoscomuni', '#select_commune_placeholder'),
                'url' => \yii\helpers\Url::to(['/comuni/default/comuni-by-provincia?soppresso=0']),
                'params' => [ $id ],
            ], !empty($comuneConfig['pluginOptions']) ? $comuneConfig['pluginOptions'] : []
        ),
        'pluginEvents' => [
            //il change viene chiamato al cambio del padre: provincia
            "depdrop:afterChange"=>"function(event, id, value, count) { 
                clearValueIfParentEmpty($(this));
             }",
        ],
    ]);

?>
</div>
