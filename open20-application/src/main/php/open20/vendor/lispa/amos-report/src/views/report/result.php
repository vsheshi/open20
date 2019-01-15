<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos-report
 * @category   CategoryName
 */
?>

<h3><?=\lispa\amos\report\AmosReport::t('amosreport', 'La segnalazione è stata inviata correttamente. ');?></h3>
<p class="pull-right">
    <?php
    $button = '<a href="' . $href . '" class="btn btn-primary" id="closeModal" title="'.\lispa\amos\report\AmosReport::t("amosreport", "La segnalazione è stata inviata correttamente.").'">' .\lispa\amos\report\AmosReport::t("amosreport", "Chiudi."). '</a>';
    echo $button;
    ?>
</p>
