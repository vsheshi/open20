<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\Constraint;

class SameSize extends Count
{
    /**
     * @var int
     */
    protected $expectedCount;

    /**
     * @param \Countable|\Traversable|array $expected
     */
    public function __construct($expected)
    {
        parent::__construct($this->getCountOf($expected));
    }
}
