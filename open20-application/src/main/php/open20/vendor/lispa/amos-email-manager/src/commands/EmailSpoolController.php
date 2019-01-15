<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */

namespace lispa\amos\emailmanager\commands;

use Exception;
use Yii;
use yii\console\Controller;
use yii\log\Logger;

class EmailSpoolController extends Controller
{

    /**
     * Sends emails
     */
    public function actionIndex($spoolLimit = 10)
    {
        try
        {
            $emailManager = Yii::$app->getModule('email');
            if ($emailManager)
            {
                $emailManager->spool($spoolLimit);
            }
        }
        catch (Exception $bex)
        {
            Yii::getLogger()->log($bex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * Sends emails in a continuous loop
     */
    public function actionLoop($loopLimit = 1000, $spoolLimit = 10)
    {
        try
        {
            $emailManager = Yii::$app->getModule('email');
            if ($emailManager)
            {
                for ($i = 0; $i < $loopLimit; $i++)
                {
                    $done = $emailManager->spool($spoolLimit);
                    if ($done)
                    {
                        for ($i = 0; $i < $done; $i++)
                        {
                            echo '.';
                        }
                    }
                    else
                    {
                        break;
                    }
                    sleep(1);
                }
            }
        }
        catch (Exception $bex)
        {
            Yii::getLogger()->log($bex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

}
