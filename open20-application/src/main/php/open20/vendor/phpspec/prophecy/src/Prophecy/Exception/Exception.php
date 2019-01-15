<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception;

/**
 * Core Prophecy exception interface.
 * All Prophecy exceptions implement it.
 *
 */
interface Exception
{
    /**
     * @return string
     */
    public function getMessage();
}
