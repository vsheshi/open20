<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\MockObject\Stub;

use PHPUnit\Framework\MockObject\Matcher\Invocation;

/**
 * Stubs a method by returning a user-defined value.
 */
interface MatcherCollection
{
    /**
     * Adds a new matcher to the collection which can be used as an expectation
     * or a stub.
     *
     * @param Invocation $matcher Matcher for invocations to mock objects
     */
    public function addMatcher(Invocation $matcher);
}
