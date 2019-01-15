<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\MockObject\Invocation;

/**
 * Represents a non-static invocation.
 */
class ObjectInvocation extends StaticInvocation
{
    /**
     * @var object
     */
    private $object;

    /**
     * @param string $className
     * @param string $methodName
     * @param array  $parameters
     * @param string $returnType
     * @param object $object
     * @param bool   $cloneObjects
     */
    public function __construct($className, $methodName, array $parameters, $returnType, $object, $cloneObjects = false)
    {
        parent::__construct($className, $methodName, $parameters, $returnType, $cloneObjects);

        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }
}
