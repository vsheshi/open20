<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework;

trait TestListenerDefaultImplementation
{
    public function addError(Test $test, \Exception $e, $time)
    {
    }

    public function addWarning(Test $test, Warning $e, $time)
    {
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
    }

    public function addIncompleteTest(Test $test, \Exception $e, $time)
    {
    }

    public function addRiskyTest(Test $test, \Exception $e, $time)
    {
    }

    public function addSkippedTest(Test $test, \Exception $e, $time)
    {
    }

    public function startTestSuite(TestSuite $suite)
    {
    }

    public function endTestSuite(TestSuite $suite)
    {
    }

    public function startTest(Test $test)
    {
    }

    public function endTest(Test $test, $time)
    {
    }
}
