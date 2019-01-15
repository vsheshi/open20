<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\chat
 * @category   CategoryName
 */

use lispa\amos\core\helpers\Html;
/**
 * @var \lispa\amos\chat\models\User $model
 * @var \lispa\amos\chat\controllers\DefaultController $appController
 * @var array $settings
 * @var int $key
 */

?>
<a class="user-contacts-item" href="<?= '/messages/' . $model->id ?>">
    <div class="item-chat media nop <?= $settings['itemCssClass'] ?>" data-key="<?= $key ?>"
         data-user-contact="<?= $model->id ?>">
        <div class="media-left">
            <div class="container-round-img">
                <?= $model->getAvatar() ?>
            </div>
        </div>
        <div class="media-body">
            <h5 class="media-heading"><strong><?= $model->name ?></strong></h5>
            <!-- < ?= $model->username ?> -->
        </div>
    </div>
</a>