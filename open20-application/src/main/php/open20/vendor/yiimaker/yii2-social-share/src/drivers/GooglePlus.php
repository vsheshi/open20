<?php
/**
 */

namespace ymaker\social\share\drivers;

use ymaker\social\share\base\Driver;

/**
 * Driver for Google Plus.
 *
 * @since 1.0
 */
class GooglePlus extends Driver
{
    /**
     * @inheritdoc
     */
    public function getLink()
    {
        $this->_link = 'https://plusone.google.com/_/+1/confirm?hl=en&url={url}';

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
