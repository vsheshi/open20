<?php
use lispa\amos\core\forms\ActiveForm;
/**
*
 **/
$this->title = \lispa\amos\groups\Module::t('amosgroups', 'Invia notifica al gruppo '). "'".$modelGroup->name."'";
$this->params['breadcrumbs'][] = $this->title;

?>
<h4><?=  \lispa\amos\groups\Module::t('amosgroups', "Invia l'email a tutti i membri del gruppo "). "<strong>".$modelGroup->name."</strong>" ?></h4>
        <?php
        //$newEmail = new \lispa\amos\groups\models\GroupsMailer();
        $form = ActiveForm::begin([
            'action' => 'send-email-to-group',
            'method' => 'post'
        ]);

        echo $form->field($model, 'idGroup')->hiddenInput()->label(false);
        echo $form->field($model, 'subject')->label(\lispa\amos\groups\Module::t('amosgroups', 'Oggetto'));
        echo $form->field($model, 'text')->textarea(['rows' => 6, 'readonly' => FALSE, 'maxlength' => 500])->label(\lispa\amos\groups\Module::t('amosgroups', 'Testo'));
        echo "<div class='pull-right'>";
                echo \lispa\amos\core\helpers\Html::a('Annulla', \yii\helpers\Url::previous(),['class' => 'btn btn-secondary m-r-5']);
                echo \lispa\amos\core\helpers\Html::submitButton('Invia',['class' => 'btn btn-primary btn-navigation-primary']);
        echo "</div>";
        ActiveForm::end();
        ?>

