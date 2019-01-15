<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class ComponentElement extends ManifestElement {
    public function getName() {
        return $this->getAttributeValue('name');
    }

    public function getVersion() {
        return $this->getAttributeValue('version');
    }
}
