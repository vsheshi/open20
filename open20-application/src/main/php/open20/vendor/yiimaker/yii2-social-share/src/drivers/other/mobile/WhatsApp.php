<?php
/**
 */

namespace ymaker\social\share\drivers\other\mobile;

use ymaker\social\share\base\Driver;

/**
 * Driver for WhatsApp messenger.
 *
 * WARNING: This driver works only in mobile devices
 * with installed WhatsApp client.
 *
 * @since 1.0
 */
class WhatsApp extends Driver
{
    /**
     * @inheritdoc
     */
    public function getLink()
    {
        $this->_link = 'whatsapp://send?text={url}';

        return parent::getLink();
    }

    /**
     * @inheritdoc
     */
    protected function processShareData()
    {
        $this->url = static::encodeData($this->url);
    }
}
