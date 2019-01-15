<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\MockObject\Matcher;

use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
 * Records invocations and provides convenience methods for checking them later
 * on.
 * This abstract class can be implemented by matchers which needs to check the
 * number of times an invocation has occurred.
 */
abstract class InvokedRecorder implements Invocation
{
    /**
     * @var BaseInvocation[]
     */
    private $invocations = [];

    /**
     * @return int
     */
    public function getInvocationCount()
    {
        return \count($this->invocations);
    }

    /**
     * @return BaseInvocation[]
     */
    public function getInvocations()
    {
        return $this->invocations;
    }

    /**
     * @return bool
     */
    public function hasBeenInvoked()
    {
        return \count($this->invocations) > 0;
    }

    /**
     * @param BaseInvocation $invocation
     */
    public function invoked(BaseInvocation $invocation)
    {
        $this->invocations[] = $invocation;
    }

    /**
     * @param BaseInvocation $invocation
     *
     * @return bool
     */
    public function matches(BaseInvocation $invocation)
    {
        return true;
    }
}
