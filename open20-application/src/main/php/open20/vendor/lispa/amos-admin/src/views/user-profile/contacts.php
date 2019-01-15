<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\views\user-profile
 * @category   CategoryName
 */


echo \lispa\amos\admin\widgets\UserContacsWidget::widget([
    'userId' => $model->user_id,
    'isUpdate' => $isUpdate
]);


?>