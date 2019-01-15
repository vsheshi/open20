<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class Proscription {
    /**
     * @var string
     */
    private $name;

    /**
     * @var Url
     */
    private $url;

    public function __construct($name, Url $url) {
        $this->name = $name;
        $this->url  = $url;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Url
     */
    public function getUrl() {
        return $this->url;
    }
}
