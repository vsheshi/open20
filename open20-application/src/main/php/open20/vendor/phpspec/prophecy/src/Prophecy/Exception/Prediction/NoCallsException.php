<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception\Prediction;

use Prophecy\Exception\Prophecy\MethodProphecyException;

class NoCallsException extends MethodProphecyException implements PredictionException
{
}
