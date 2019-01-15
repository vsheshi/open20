<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception\Prediction;

use RuntimeException;

/**
 * Basic failed prediction exception.
 * Use it for custom prediction failures.
 *
 */
class FailedPredictionException extends RuntimeException implements PredictionException
{
}
