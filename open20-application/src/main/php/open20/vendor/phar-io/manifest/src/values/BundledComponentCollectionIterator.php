<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class BundledComponentCollectionIterator implements \Iterator {
    /**
     * @var BundledComponent[]
     */
    private $bundledComponents = [];

    /**
     * @var int
     */
    private $position;

    public function __construct(BundledComponentCollection $bundledComponents) {
        $this->bundledComponents = $bundledComponents->getBundledComponents();
    }

    public function rewind() {
        $this->position = 0;
    }

    /**
     * @return bool
     */
    public function valid() {
        return $this->position < count($this->bundledComponents);
    }

    /**
     * @return int
     */
    public function key() {
        return $this->position;
    }

    /**
     * @return BundledComponent
     */
    public function current() {
        return $this->bundledComponents[$this->position];
    }

    public function next() {
        $this->position++;
    }
}
