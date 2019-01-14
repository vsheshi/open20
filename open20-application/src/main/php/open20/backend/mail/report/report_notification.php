<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\report\views\report\email
 * @category   CategoryName
 */

use lispa\amos\core\helpers\Html;
use lispa\amos\report\AmosReport;

/**
 * @var \lispa\amos\core\interfaces\ContentModelInterface $contentModel
 * @var \lispa\amos\report\models\base\Report $report
 * @var \lispa\amos\admin\models\UserProfile $reportCreatorProfile
 */

?>

<div>
    <div style="box-sizing:border-box;">
        <div style="padding:5px 10px;background-color: #F2F2F2;">
            <h1 style="color:#297A38;text-align:center;font-size:1.5em;margin:0;"><?= $reportCreatorProfile->getNomeCognome() . " " . AmosReport::t('amosreport', "#sent_a_report") ?></h1>
        </div>
    </div>

    <div style="border:1px solid #cccccc;padding:10px;margin-bottom: 10px;background-color: #ffffff;margin-top:20px">
        <div style="box-sizing:border-box;font-size:13px;font-weight:normal;">
            <p><?= \Yii::t('app', 'Gentile ') . '<strong>'.$contentCreator->userProfile->nomeCognome.'</strong>'?></p>
            <?php if ($reportCreatorProfile != null): ?>
                <p><strong><?= $reportCreatorProfile->getNomeCognome(). '</strong>'  . " " . AmosReport::t('amosreport', "#sent_a_report")
                    . ' ' . AmosReport::t('amosreport', "#for")
                    . " " . $contentModel->getGrammar()->getArticleSingular()
                    . " " . $contentModel->getGrammar()->getModelSingularLabel()
                    . " " . "'" . $contentModel->getTitle(). "'"
                    . ", che hai creato" ?></p>
            <?php endif; ?>
            <div>
                <div">
                    <p><strong>Segnalazione: </strong>
                        <i><?= $report->content?></i>
                    </p>
                </div>
            </div>
        <?php $updateUrl = str_replace(['/view', '/partecipa'], '/update', $contentModel->getFullViewUrl()). '#tab-reports';?>
            <p><?= Html::a(\Yii::t('app', 'Modera'), \Yii::$app->urlManager->createAbsoluteUrl($updateUrl), ['style' => 'color: green;']) ?></p>
            <p>Grazie,<br>
                lo Staff PCDoc</p>
        </div>
    </div>
</div>
