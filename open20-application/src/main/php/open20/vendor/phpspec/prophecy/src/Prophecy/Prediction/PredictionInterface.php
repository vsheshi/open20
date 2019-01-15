<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Prediction;

use Prophecy\Call\Call;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Prediction interface.
 * Predictions are logical test blocks, tied to `should...` keyword.
 *
 */
interface PredictionInterface
{
    /**
     * Tests that double fulfilled prediction.
     *
     * @param Call[]        $calls
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @throws object
     * @return void
     */
    public function check(array $calls, ObjectProphecy $object, MethodProphecy $method);
}
