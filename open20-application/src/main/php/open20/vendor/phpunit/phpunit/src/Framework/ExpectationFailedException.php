<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework;

use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Exception for expectations which failed their check.
 *
 * The exception contains the error message and optionally a
 * SebastianBergmann\Comparator\ComparisonFailure which is used to
 * generate diff output of the failed expectations.
 */
class ExpectationFailedException extends AssertionFailedError
{
    protected $comparisonFailure;

    /**
     * @param string                 $message
     * @param ComparisonFailure|null $comparisonFailure
     * @param \Exception|null        $previous
     */
    public function __construct($message, ComparisonFailure $comparisonFailure = null, \Exception $previous = null)
    {
        $this->comparisonFailure = $comparisonFailure;

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return null|ComparisonFailure
     */
    public function getComparisonFailure()
    {
        return $this->comparisonFailure;
    }
}
