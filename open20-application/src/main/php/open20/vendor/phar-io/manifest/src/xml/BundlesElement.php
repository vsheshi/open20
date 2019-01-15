<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class BundlesElement extends ManifestElement {
    public function getComponentElements() {
        return new ComponentElementCollection(
            $this->getChildrenByName('component')
        );
    }
}
