<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PHPUnit\Framework;

use Countable;

/**
 * A Test can be run and collect its results.
 */
interface Test extends Countable
{
    /**
     * Runs a test and collects its result in a TestResult instance.
     *
     * @param TestResult $result
     *
     * @return TestResult
     */
    public function run(TestResult $result = null);
}
