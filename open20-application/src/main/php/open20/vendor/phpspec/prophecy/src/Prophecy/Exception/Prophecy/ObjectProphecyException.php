<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception\Prophecy;

use Prophecy\Prophecy\ObjectProphecy;

class ObjectProphecyException extends \RuntimeException implements ProphecyException
{
    private $objectProphecy;

    public function __construct($message, ObjectProphecy $objectProphecy)
    {
        parent::__construct($message);

        $this->objectProphecy = $objectProphecy;
    }

    /**
     * @return ObjectProphecy
     */
    public function getObjectProphecy()
    {
        return $this->objectProphecy;
    }
}
