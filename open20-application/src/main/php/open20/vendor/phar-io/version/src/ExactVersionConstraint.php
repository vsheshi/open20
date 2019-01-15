<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Version;

class ExactVersionConstraint extends AbstractVersionConstraint {
    /**
     * @param Version $version
     *
     * @return bool
     */
    public function complies(Version $version) {
        return $this->asString() == $version->getVersionString();
    }
}
