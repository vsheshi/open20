<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception\Doubler;

use ReflectionClass;

class ClassMirrorException extends \RuntimeException implements DoublerException
{
    private $class;

    public function __construct($message, ReflectionClass $class)
    {
        parent::__construct($message);

        $this->class = $class;
    }

    public function getReflectedClass()
    {
        return $this->class;
    }
}
