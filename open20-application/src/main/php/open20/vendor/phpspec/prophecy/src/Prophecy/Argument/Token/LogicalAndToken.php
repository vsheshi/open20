<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Argument\Token;

/**
 * Logical AND token.
 *
 */
class LogicalAndToken implements TokenInterface
{
    private $tokens = array();

    /**
     * @param array $arguments exact values or tokens
     */
    public function __construct(array $arguments)
    {
        foreach ($arguments as $argument) {
            if (!$argument instanceof TokenInterface) {
                $argument = new ExactValueToken($argument);
            }
            $this->tokens[] = $argument;
        }
    }

    /**
     * Scores maximum score from scores returned by tokens for this argument if all of them score.
     *
     * @param $argument
     *
     * @return bool|int
     */
    public function scoreArgument($argument)
    {
        if (0 === count($this->tokens)) {
            return false;
        }

        $maxScore = 0;
        foreach ($this->tokens as $token) {
            $score = $token->scoreArgument($argument);
            if (false === $score) {
                return false;
            }
            $maxScore = max($score, $maxScore);
        }

        return $maxScore;
    }

    /**
     * Returns false.
     *
     * @return boolean
     */
    public function isLast()
    {
        return false;
    }

    /**
     * Returns string representation for token.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('bool(%s)', implode(' AND ', $this->tokens));
    }
}
