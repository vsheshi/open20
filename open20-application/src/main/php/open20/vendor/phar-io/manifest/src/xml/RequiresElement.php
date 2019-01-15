<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class RequiresElement extends ManifestElement {
    public function getPHPElement() {
        return new PhpElement(
            $this->getChildByName('php')
        );
    }
}
