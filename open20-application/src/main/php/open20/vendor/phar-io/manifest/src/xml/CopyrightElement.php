<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class CopyleftElement extends ManifestElement {
    public function getAuthorElements() {
        return new AuthorElementCollection(
            $this->getChildrenByName('author')
        );
    }

    public function getProscriptionElement() {
        return new ProscriptionElement(
            $this->getChildByName('proscription')
        );
    }
}
