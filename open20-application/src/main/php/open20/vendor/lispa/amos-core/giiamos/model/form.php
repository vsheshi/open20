<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\giiamos\model
 * @category   CategoryName
 */

use yii\gii\generators\model\Generator;
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var yii\gii\generators\form\Generator $generator
 */

echo $form->field($generator, 'tableName');
echo $form->field($generator, 'tablePrefix');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'pluginName');
echo $form->field($generator, 'db');
echo $form->field($generator, 'generateRelations')->dropDownList([
    Generator::RELATIONS_NONE => 'No relations',
    Generator::RELATIONS_ALL => 'All relations',
    Generator::RELATIONS_ALL_INVERSE => 'All relations with inverse',
]);
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateModelClass')->checkbox();
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');

echo $form->field($generator, 'migrationName')->textInput(['readonly'=> true])->label(false);
