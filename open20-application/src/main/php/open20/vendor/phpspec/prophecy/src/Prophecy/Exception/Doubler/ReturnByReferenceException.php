<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception\Doubler;

class ReturnByReferenceException extends DoubleException
{
    private $classname;
    private $methodName;

    /**
     * @param string $message
     * @param string $classname
     * @param string $methodName
     */
    public function __construct($message, $classname, $methodName)
    {
        parent::__construct($message);

        $this->classname  = $classname;
        $this->methodName = $methodName;
    }

    public function getClassname()
    {
        return $this->classname;
    }

    public function getMethodName()
    {
        return $this->methodName;
    }
}
