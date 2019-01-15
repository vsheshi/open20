<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\forms\views\widgets
 * @category   CategoryName
 */

use lispa\amos\core\forms\InteractionMenuWidget;
use lispa\amos\core\module\BaseAmosModule;

/**
 * @var string $contentCreatorAvatar Avatar of the content creator.
 * @var string $contentCreatorNameSurname Name and surname of the content creator.
 * @var \lispa\amos\admin\models\UserProfile $contentCreatorModel.
 * @var bool $hideInteractionMenu If true set the class to hide the interaction menu.
 * @var array $interactionMenuButtons Sets the interaction menu buttons.
 * @var array $interactionMenuButtonsHide Sets the interaction menu buttons to hide.
 * @var string $publicatonDate Publication date of the content.
 */
?>

<div class="post-header col-xs-12 nop media">
    <div class="post-header-left media-left">
        <?= $contentCreatorAvatar ?>
    </div>
    <div class="media-body">
        <p class="creator"><?= \lispa\amos\core\helpers\Html::a($contentCreatorNameSurname, ['/admin/user-profile/view', 'id' => $contentCreatorModel->id], [
                'title' => BaseAmosModule::t('amoscore', 'Apri il profilo di {user_profile_name}', ['user_profile_name' => $contentCreatorNameSurname])
            ]) ?></p>
        <?= InteractionMenuWidget::widget([
            'hideInteractionMenu' => $hideInteractionMenu,
            'interactionMenuButtons' => $interactionMenuButtons,
            'interactionMenuButtonsHide' => $interactionMenuButtonsHide,
            'model' => $model
        ]) ?>
        <?php if ($publicatonDate): ?>
            <p><?= BaseAmosModule::t('amoscore', 'Pubblicato il') ?> <?= $publicatonDate ?></p>
        <?php endif; ?>
    </div>
</div>
