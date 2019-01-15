<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\MockObject\Stub;

use PHPUnit\Framework\MockObject\Invocation;
use PHPUnit\Framework\MockObject\Invocation\ObjectInvocation;
use PHPUnit\Framework\MockObject\RuntimeException;
use PHPUnit\Framework\MockObject\Stub;

/**
 * Stubs a method by returning the current object.
 */
class ReturnSelf implements Stub
{
    public function invoke(Invocation $invocation)
    {
        if (!$invocation instanceof ObjectInvocation) {
            throw new RuntimeException(
                'The current object can only be returned when mocking an ' .
                'object, not a static class.'
            );
        }

        return $invocation->getObject();
    }

    public function toString()
    {
        return 'return the current object';
    }
}
