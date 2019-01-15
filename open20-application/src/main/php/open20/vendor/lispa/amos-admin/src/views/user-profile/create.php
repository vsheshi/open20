<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile
 * @category   CategoryName
 */

use lispa\amos\admin\AmosAdmin;

/**
 * @var yii\web\View $this
 * @var lispa\amos\admin\models\UserProfile $model
 * @var bool $permissionSave
 */

$this->title = AmosAdmin::t('amosadmin', 'Crea');
$this->params['breadcrumbs'][] = ['label' => AmosAdmin::t('amosadmin', 'Utenti'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-content user-profile-create">
    <?= $this->render('_form', [
        'model' => $model,
        'user' => $user,
        'permissionSave' => $permissionSave,
    ]) ?>
</div>
