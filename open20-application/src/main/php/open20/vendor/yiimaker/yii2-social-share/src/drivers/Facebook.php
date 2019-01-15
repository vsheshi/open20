<?php
/**
 */

namespace ymaker\social\share\drivers;

use ymaker\social\share\base\Driver;

/**
 * Driver for Facebook.
 *
 * @since 1.0
 */
class Facebook extends Driver
{
    /**
     * @inheritdoc
     */
    public function getLink()
    {
        $this->_link = 'http://www.facebook.com/sharer.php?u={url}';
        $this->_metaTags = [
            ['property' => 'og:url',         'content' => '{url}'],
            ['property' => 'og:type',        'content' => 'website'],
            ['property' => 'og:title',       'content' => '{title}'],
            ['property' => 'og:description', 'content' => '{description}'],
            ['property' => 'og:image',       'content' => '{imageUrl}'],
        ];

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
