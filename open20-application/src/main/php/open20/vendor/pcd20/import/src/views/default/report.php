<?php
$this->title = "Report Aree importate";
?>
<div class="col-lg-12">
    <p>Scarica il report o ritorna alla dashboard.</p>
</div><br><br>
<div class="col-lg-12">
    <?= \yii\helpers\Html::a('Scarica report', ['/import/default/generate-excel', 'id' => $importation->id], ['class' => 'btn btn-navigation-primary']);?>

    <?php // \yii\helpers\Html::a("Vai all'area importata", ['/community/join/index' , 'id' => $communityCreated], ['class' => 'btn btn-navigation-primary']); ?>
</div>