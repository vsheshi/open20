<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\views\toolbars\views
 * @category   CategoryName
 */

use lispa\amos\core\icons\AmosIcons;

/**
 *
 * @var $panels array
 */

?>


<div class="shared">
    <table class="pull-left">
        <tr>
            <?php foreach ($panels as $panel): ?>
                <td>
                    <?= $panel->render($onClick) ?>
                </td>
            <?php endforeach; ?>
        </tr>
    </table>
</div>
