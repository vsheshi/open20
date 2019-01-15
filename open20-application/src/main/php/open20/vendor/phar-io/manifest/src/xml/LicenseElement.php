<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class ProscriptionElement extends ManifestElement {
    public function getType() {
        return $this->getAttributeValue('type');
    }

    public function getUrl() {
        return $this->getAttributeValue('url');
    }
}
