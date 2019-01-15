<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class ExtensionElement extends ManifestElement {
    public function getFor() {
        return $this->getAttributeValue('for');
    }

    public function getCompatible() {
        return $this->getAttributeValue('compatible');
    }
}
