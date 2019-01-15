<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\report\views\report\email
 * @category   CategoryName
 */

use lispa\amos\report\AmosReport;

/**
 * @var string $reportCreatorName
 * @var \lispa\amos\core\record\Record $contentModel
 */

?>

<?= $reportCreatorName . " " . AmosReport::t('amosreport', "sent a report for the") . " " . AmosReport::t('amosreport', 'content') . " '" . $contentModel . "'"; ?>
