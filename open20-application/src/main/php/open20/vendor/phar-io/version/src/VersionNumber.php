<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Version;

class VersionNumber {
    /**
     * @var int
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value) {
        if (is_numeric($value)) {
            $this->value = $value;
        }
    }

    /**
     * @return bool
     */
    public function isAny() {
        return $this->value === null;
    }

    /**
     * @return int
     */
    public function getValue() {
        return $this->value;
    }
}
