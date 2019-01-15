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
use lispa\amos\comuni\models\IstatComuni;
use lispa\amos\core\forms\editors\Select;
use yii\helpers\ArrayHelper;

//id del campo: se specificato nelle option uso quello, altrimenti sarà nel formato 'campo_db-id'
$comuneAttribute = $comuneConfig['attribute'];
$id = isset($comuneConfig['options']['id']) ? $comuneConfig['options']['id'] : $comuneAttribute.'-id';

?>

<div class="<?= isset($comuneConfig['class']) ? $comuneConfig['class'] : 'col-xs-3';?>">
    <?= $form->field($model, 'commune_id')->widget(Select::classname(), [
        'options' =>  array_merge(
            [
                'id' => $id,
                'placeholder' => AmosComuni::t('amoscomuni', '#select_commune_placeholder'),
            ], !empty($comuneConfig['options']) ? $comuneConfig['options'] : []
        ),
        'pluginOptions' => array_merge(
            [
                'allowClear' => true
            ], !empty($comuneConfig['pluginOptions']) ? $comuneConfig['pluginOptions'] : []
        ),
        'data' => ArrayHelper::map(IstatComuni::find()->orderBy('nome')->asArray()->all(), 'id', 'nome')
    ]); ?>
</div>


