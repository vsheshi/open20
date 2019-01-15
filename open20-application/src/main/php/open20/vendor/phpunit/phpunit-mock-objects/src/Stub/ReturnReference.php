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
 * Stubs a method by returning a user-defined reference to a value.
 */
class ReturnReference implements Stub
{
    /**
     * @var mixed
     */
    private $reference;

    public function __construct(&$reference)
    {
        $this->reference = &$reference;
    }

    public function invoke(Invocation $invocation)
    {
        return $this->reference;
    }

    public function toString()
    {
        $exporter = new Exporter;

        return \sprintf(
            'return user-specified reference %s',
            $exporter->export($this->reference)
        );
    }
}
