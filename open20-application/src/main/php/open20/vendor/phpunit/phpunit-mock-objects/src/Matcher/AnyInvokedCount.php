<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\MockObject\Matcher;

/**
 * Invocation matcher which checks if a method has been invoked zero or more
 * times. This matcher will always match.
 */
class AnyInvokedCount extends InvokedRecorder
{
    /**
     * @return string
     */
    public function toString()
    {
        return 'invoked zero or more times';
    }

    public function verify()
    {
    }
}
