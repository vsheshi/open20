<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

use lispa\amos\community\AmosCommunity;
use lispa\amos\core\helpers\Html;

/** @var \lispa\amos\community\utilities\EmailUtil $util */

?>

<div>
    <div style="box-sizing:border-box;">
        <div style="padding:5px 10px;background-color: #F2F2F2;">
            <h1 style="color:#297A38;text-align:center;font-size:1.5em;margin:0;"><?= AmosCommunity::t('amoscommunity', '#change_role_mail_title') ?></h1>
        </div>
        <div style="border:1px solid #cccccc;padding:10px;margin-bottom: 10px;background-color: #ffffff; margin-top: 20px; font-weight: normal">
            <?php if (!empty($userName)): ?>
                <p><?= \Yii::t('app', 'Gentile <strong>'. $userName . '</strong>')?></p>
            <?php endif; ?>
            <p style="color:#000000;"><?= \Yii::t('app', 'Il tuo ruolo nella area di lavoro <strong>{community_name}</strong> è stato modificato', ['community_name' => $util->community->name ])  ?></p>

            <div style="color:#000000;width:100%;">
                <p>
                    <?= AmosCommunity::t('amoscommunity', "#your_role") . " " ?>
                    <span style="font-weight: 900"><?= AmosCommunity::t('amoscommunity', $util->role) . "." ?></span>
                    <?= AmosCommunity::t('amoscommunity',"#text_change_role") ?>
                </p>
				<p>  
				<?= AmosCommunity::t('amoscommunity',"Grazie, ") ?><br>
				<?= AmosCommunity::t('amoscommunity',"lo Staff PCDoc") ?>
				</p>
            </div>

        </div>
    </div>
</div>