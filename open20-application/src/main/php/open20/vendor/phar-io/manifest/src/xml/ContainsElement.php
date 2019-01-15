<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class ContainsElement extends ManifestElement {
    public function getName() {
        return $this->getAttributeValue('name');
    }

    public function getVersion() {
        return $this->getAttributeValue('version');
    }

    public function getType() {
        return $this->getAttributeValue('type');
    }

    public function getExtensionElement() {
        return new ExtensionElement(
            $this->getChildByName('extension')
        );
    }
}
