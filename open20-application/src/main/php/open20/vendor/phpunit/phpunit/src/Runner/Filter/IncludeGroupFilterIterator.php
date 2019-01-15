<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Runner\Filter;

class IncludeGroupFilterIterator extends GroupFilterIterator
{
    /**
     * @param string $hash
     *
     * @return bool
     */
    protected function doAccept($hash)
    {
        return \in_array($hash, $this->groupTests);
    }
}
