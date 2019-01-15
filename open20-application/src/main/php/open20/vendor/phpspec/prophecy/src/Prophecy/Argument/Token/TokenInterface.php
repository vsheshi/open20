<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Argument\Token;

/**
 * Argument token interface.
 *
 */
interface TokenInterface
{
    /**
     * Calculates token match score for provided argument.
     *
     * @param $argument
     *
     * @return bool|int
     */
    public function scoreArgument($argument);

    /**
     * Returns true if this token prevents check of other tokens (is last one).
     *
     * @return bool|int
     */
    public function isLast();

    /**
     * Returns string representation for token.
     *
     * @return string
     */
    public function __toString();
}
