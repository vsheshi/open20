<?php

use lispa\amos\dashboard\AmosDashboard;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
?>

<div class="card-widget">
    <div class="chechbox-widget pull-right">
        <label for="<?=\yii\helpers\StringHelper::basename($model['classname']);?>" class="sr-only"><?= Yii::createObject($model['classname'])->getDescription(); ?></label>
        <input id="<?=\yii\helpers\StringHelper::basename($model['classname']);?>" type="checkbox" name="amosWidgetsClassnames[]" value="<?=$model['classname'];?>" <?= (in_array($model['classname'], $this->params['widgetSelected'])? 'checked' : '') ?> />
    </div>
    <?php
        $object = \Yii::createObject($model['classname']);
        $object->setUrl('');
    ?>
    <?= $object->run(); ?>
    <p><?= Yii::createObject($model['classname'])->getDescription(); ?></p>
</div>
