<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception\Prophecy;

use Prophecy\Prophecy\MethodProphecy;

class MethodProphecyException extends ObjectProphecyException
{
    private $methodProphecy;

    public function __construct($message, MethodProphecy $methodProphecy)
    {
        parent::__construct($message, $methodProphecy->getObjectProphecy());

        $this->methodProphecy = $methodProphecy;
    }

    /**
     * @return MethodProphecy
     */
    public function getMethodProphecy()
    {
        return $this->methodProphecy;
    }
}
