<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Comparator;

use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Closure comparator.
 *
 */
final class ClosureComparator extends Comparator
{
    public function accepts($expected, $actual)
    {
        return is_object($expected) && $expected instanceof \Closure
            && is_object($actual) && $actual instanceof \Closure;
    }

    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        throw new ComparisonFailure(
            $expected,
            $actual,
            // we don't need a diff
            '',
            '',
            false,
            'all closures are born different'
        );
    }
}
