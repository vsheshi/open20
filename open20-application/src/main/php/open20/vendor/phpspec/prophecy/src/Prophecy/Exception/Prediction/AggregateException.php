<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception\Prediction;

use Prophecy\Prophecy\ObjectProphecy;

class AggregateException extends \RuntimeException implements PredictionException
{
    private $exceptions = array();
    private $objectProphecy;

    public function append(PredictionException $exception)
    {
        $message = $exception->getMessage();
        $message = '  '.strtr($message, array("\n" => "\n  "))."\n";

        $this->message      = rtrim($this->message.$message);
        $this->exceptions[] = $exception;
    }

    /**
     * @return PredictionException[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    public function setObjectProphecy(ObjectProphecy $objectProphecy)
    {
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
