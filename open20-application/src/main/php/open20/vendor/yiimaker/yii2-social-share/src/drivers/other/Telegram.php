<?php
/**
 */

namespace ymaker\social\share\drivers\other;

use ymaker\social\share\base\Driver;

/**
 * Driver for Telegram messenger.
 *
 * @since 1.0
 */
class Telegram extends Driver
{
    /**
     * @var bool|string
     */
    public $message = false;


    /**
     * @inheritdoc
     */
    public function getLink()
    {
        $this->_link = 'https://telegram.me/share/url?url={url}';

        if ($this->message) {
            $this->_data['{message}'] = $this->message;
            $this->addUrlParam('text', '{message}');
        }

        return parent::getLink();
    }

    /**
     * @inheritdoc
     */
    protected function processShareData()
    {
        $this->url = static::encodeData($this->url);
        if (is_string($this->message)) {
            $this->message = static::encodeData($this->message);
        }
    }
}
