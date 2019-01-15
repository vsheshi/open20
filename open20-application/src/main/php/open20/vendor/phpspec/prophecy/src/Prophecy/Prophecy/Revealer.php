<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Prophecy;

/**
 * Basic prophecies revealer.
 *
 */
class Revealer implements RevealerInterface
{
    /**
     * Unwraps value(s).
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function reveal($value)
    {
        if (is_array($value)) {
            return array_map(array($this, __FUNCTION__), $value);
        }

        if (!is_object($value)) {
            return $value;
        }

        if ($value instanceof ProphecyInterface) {
            $value = $value->reveal();
        }

        return $value;
    }
}
