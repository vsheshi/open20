<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class ComponentElementCollection extends ElementCollection {
    public function current() {
        return new ComponentElement(
            $this->getCurrentElement()
        );
    }
}
