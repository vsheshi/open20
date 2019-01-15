<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\email
 * @category   CategoryName
 */

namespace lispa\amos\emailmanager\transports;

use lispa\amos\emailmanager\interfaces\TransportInterface;
use Exception;
use Yii;
use yii\base\Component;
use yii\log\Logger;
use yii\mail\MailerInterface;

class YiiMailer extends Component implements TransportInterface
{

    public function send($from, $to, $subject, $text, $files = [], $bcc = null)
    {
        $ret = '';
        try
        {
            /** @var MailerInterface $mailer */
            $mailer = \Yii::$app->get('mailer');

            if ($mailer != null)
            {
                $message = $mailer->compose()
                        ->setFrom($this->parseFrom($from))
                        ->setTo($to)
                        ->setSubject($subject)
                        ->setHtmlBody($text);

                if ($bcc)
                {
                    $message->setBcc($bcc);
                }

                if (count($files) > 0)
                {
                    foreach ($files as $file)
                    {
                        //$message->attach($filePath);
                        $message->attachContent($file->content, ['fileName' => $file->name, 'contentType' => $file->type]);
                    }
                }
                $ret = $message->send();
            }
        }
        catch (Exception $bex)
        {
            Yii::getLogger()->log($bex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $ret;
    }

    /**
     * Quick workaround for sender email
     *
     * @param $from
     * @return string|array
     */
    protected function parseFrom($from)
    {
        $parts = explode(' ', $from);

        if (count($parts) == 1)
        {
            return $from;
        }

        $email = array_shift($parts);
        $name = implode(' ', $parts);
        return [$email => $name];
    }

    /**
     * @param string $string
     * @return array|string
     */
    public function parseRecipients($string)
    {
        $parts = explode(',', $string);
        return $parts;
    }

}
