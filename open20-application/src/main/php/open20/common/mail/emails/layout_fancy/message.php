<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */


$appLink = Yii::$app->urlManager->createAbsoluteUrl(['/']);
$appName = Yii::$app->name;
?>
<table width="600" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td style="margin-bottom:10px;background-color:#297A38;height:15px"></td>
        <td style="margin-bottom:10px;background-color:#297A38;height:15px"></td>
        <td style="margin-bottom:10px;background-color:#297A38;height:15px"></td>
    </tr>
    <tr>
        <td style="height:10px"></td>
    </tr>
    <tr style="background-color:#ffffff;">
        <td>
            <?php if (isset(Yii::$app->params['logoMail'])) {
                $logoMail = $appLink . Yii::$app->params['logoMail'];
            } else {
                $logoMail = '';
            } ?>
            <img style="max-width:500px; max-height:60px;" src="<?= $logoMail ?>" alt="logo">
        </td>
    </tr>
</table>

<?php if ($heading) { ?>
    <table width=" 600" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td align="center" valign="top">
                <h1 style="padding-top: 25px; color:green;margin:0;display:block;font-family:Arial;font-size:25px;font-weight:bold;text-align:center;line-height:150%"><?php echo $heading; ?></h1>
            </td>
        </tr>
    </table>
<?php } ?>


<table width=" 600" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td>
            <div class="corpo"
                 style="padding:10px;margin-bottom:10px;background-color:#fff;">
                <?php echo $contents; ?>
            </div>
        </td>
    </tr>
</table>

<table width="600" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td>
            <!--            <div style="color:black; background-color:lightgrey; padding:10px; font-family:Arial;font-size:12px;line-height:150%;text-align:left">-->
            <div style="font-style: italic; color: #b0b0b0; margin-top:10px;border-top: 2px solid #297a38;padding-top: 5px;font-size: 11px;line-height: normal;">
                <?= Yii::t('amosapp', '#footer_template_mail', [
                    'appName' => $appName,
                ]) ?>
                <p style="margin: 0px;"><a href="<?= $appLink ?>site/privacy"
                      title="<?= Yii::t('amosapp', '#footer_template_mail_privacy_title') ?>"
                      target="_blank"><?= Yii::t('amosapp', '#footer_template_mail_privacy') ?></a></p>
            </div>
        </td>
    </tr>
</table>
