<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\MockObject\Builder;

/**
 * Builder interface for invocation order matches.
 */
interface Match extends Stub
{
    /**
     * Defines the expectation which must occur before the current is valid.
     *
     * @param string $id The identification of the expectation that should
     *                   occur before this one.
     *
     * @return Stub
     */
    public function after($id);
}
