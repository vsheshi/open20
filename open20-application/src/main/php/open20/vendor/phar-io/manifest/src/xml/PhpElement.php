<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class PhpElement extends ManifestElement {
    public function getVersion() {
        return $this->getAttributeValue('version');
    }

    public function hasExtElements() {
        return $this->hasChild('ext');
    }

    public function getExtElements() {
        return new ExtElementCollection(
            $this->getChildrenByName('ext')
        );
    }
}
