<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class RequirementCollection implements \Countable, \IteratorAggregate {
    /**
     * @var Requirement[]
     */
    private $requirements = [];

    public function add(Requirement $requirement) {
        $this->requirements[] = $requirement;
    }

    /**
     * @return Requirement[]
     */
    public function getRequirements() {
        return $this->requirements;
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->requirements);
    }

    /**
     * @return RequirementCollectionIterator
     */
    public function getIterator() {
        return new RequirementCollectionIterator($this);
    }
}
