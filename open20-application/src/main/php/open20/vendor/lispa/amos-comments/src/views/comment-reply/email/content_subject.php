<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comments\views\comment\email
 * @category   CategoryName
 */

use lispa\amos\comments\AmosComments;

/**
 * @var \lispa\amos\core\record\Record $contextModel
 */

?>

<?= AmosComments::t('amoscomments', '#notification_email_subject', [$contextModel->getGrammar()->getModelSingularLabel(), $contextModel->getTitle()]); ?>
