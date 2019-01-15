<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Prophecy;

/**
 * Core Prophecy interface.
 *
 */
interface ProphecyInterface
{
    /**
     * Reveals prophecy object (double) .
     *
     * @return object
     */
    public function reveal();
}
