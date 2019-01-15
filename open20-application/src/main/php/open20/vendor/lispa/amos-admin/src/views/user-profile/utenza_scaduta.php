<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile
 * @category   CategoryName
 */

use lispa\amos\core\icons\AmosIcons;
use lispa\amos\admin\AmosAdmin;
use lispa\amos\core\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
$this->title = Yii::t('amosadmin', '#password_expired');
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- logo -->
<div id="bk-formDefaultLogin" class="bk-loginContainer">
    <h2><?= Html::encode($this->title) ?></h2>
    <hr class="bk-hrLogin">
    <p><?= $message ?></p>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="form-group">
            </div>
            <div class="clear"></div>
           <?= Html::a(AmosIcons::show('unlock') . AmosAdmin::t('amosadmin', 'Cambia password'), ['/admin/user-profile/cambia-password', 'id' => $user_id], [
                        'class' => 'btn  btn-action-primary btn-cambia-password'
                    ]); ?>
        </div>
    </div>
</div>