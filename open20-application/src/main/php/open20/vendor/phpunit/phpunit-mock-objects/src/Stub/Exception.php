<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\MockObject\Stub;

use PHPUnit\Framework\MockObject\Invocation;
use PHPUnit\Framework\MockObject\Stub;
use SebastianBergmann\Exporter\Exporter;

/**
 * Stubs a method by raising a user-defined exception.
 */
class Exception implements Stub
{
    private $exception;

    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function invoke(Invocation $invocation)
    {
        throw $this->exception;
    }

    public function toString()
    {
        $exporter = new Exporter;

        return \sprintf(
            'raise user-specified exception %s',
            $exporter->export($this->exception)
        );
    }
}
