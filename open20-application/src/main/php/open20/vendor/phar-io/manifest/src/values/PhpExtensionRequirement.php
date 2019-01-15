<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class PhpExtensionRequirement implements Requirement {
    /**
     * @var string
     */
    private $extension;

    /**
     * @param string $extension
     */
    public function __construct($extension) {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->extension;
    }
}
