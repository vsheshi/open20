<?php
/**
 */

namespace ymaker\social\share\drivers\other\mobile;

use ymaker\social\share\base\Driver;

/**
 * Driver for Viber messenger.
 *
 * WARNING: This driver works only in mobile devices
 * with installed Viber client.
 *
 * @since 1.0
 */
class Viber extends Driver
{
    /**
     * @inheritdoc
     */
    public function getLink()
    {
        $this->_link = 'viber://forward?text={url}';

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
