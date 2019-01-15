<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Argument\Token;

/**
 * Approximate value token
 *
 */
class ApproximateValueToken implements TokenInterface
{
    private $value;
    private $precision;

    public function __construct($value, $precision = 0)
    {
        $this->value = $value;
        $this->precision = $precision;
    }

    /**
     * {@inheritdoc}
     */
    public function scoreArgument($argument)
    {
        return round($argument, $this->precision) === round($this->value, $this->precision) ? 10 : false;
    }

    /**
     * {@inheritdoc}
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
        return sprintf('≅%s', round($this->value, $this->precision));
    }
}
