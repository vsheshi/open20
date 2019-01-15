<?php
/**
 */

namespace ymaker\social\share\drivers;

use ymaker\social\share\base\Driver;

/**
 * Driver for Twitter.
 *
 * @since 1.0
 */
class Twitter extends Driver
{
    /**
     * @var bool|string
     */
    public $account = false;


    /**
     * @inheritdoc
     */
    public function getLink()
    {
        $this->_link = 'http://twitter.com/share?'
                    . 'url={url}'
                    . '&text={description}';

        if ($this->account) {
            $this->_data['{account}'] = $this->account;
            $this->addUrlParam('via', '{account}');
        }

        $this->_metaTags = [
            ['name' => 'twitter:card',         'content' => 'summary_large_image'],
            ['name' => 'twitter:title',        'content' => '{title}'],
            ['name' => 'twitter:description',  'content' => '{description}'],
            ['name' => 'twitter:image',        'content' => '{imageUrl}']
        ];

        return parent::getLink();
    }

    /**
     * @inheritdoc
     */
    protected function processShareData()
    {
        $this->url = static::encodeData($this->url);
        $this->description = static::encodeData($this->description);

        if (is_string($this->account)) {
            $this->account = static::encodeData($this->account);
        }
    }
}
