<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\mail\user
 * @category   CategoryName
 */

use lispa\amos\admin\utility\UserProfileUtility;

/**
 * @var \lispa\amos\admin\models\UserProfile $profile
 */

?>

<?= UserProfileUtility::generateSubject($profile) ?>
