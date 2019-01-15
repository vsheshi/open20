<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\MockObject\Matcher;

use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
 * Invocation matcher which allows any parameters to a method.
 */
class AnyParameters extends StatelessInvocation
{
    /**
     * @return string
     */
    public function toString()
    {
        return 'with any parameters';
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
