<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class AuthorElementCollection extends ElementCollection {
    public function current() {
        return new AuthorElement(
            $this->getCurrentElement()
        );
    }
}
