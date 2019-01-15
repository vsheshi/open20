<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

use openinnovation\organizations\widgets\UserNetworkWidget;

echo UserNetworkWidget::widget([
    'userId' => $userId,
    'isUpdate' => $isUpdate
]);


?>