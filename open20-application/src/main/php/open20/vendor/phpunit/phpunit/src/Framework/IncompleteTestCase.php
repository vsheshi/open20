<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework;

/**
 * An incomplete test case
 */
class IncompleteTestCase extends TestCase
{
    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var bool
     */
    protected $backupGlobals = false;

    /**
     * @var bool
     */
    protected $backupStaticAttributes = false;

    /**
     * @var bool
     */
    protected $runTestInSeparateProcess = false;

    /**
     * @var bool
     */
    protected $useErrorHandler = false;

    /**
     * @var bool
     */
    protected $useOutputBuffering = false;

    /**
     * @param string $className
     * @param string $methodName
     * @param string $message
     */
    public function __construct($className, $methodName, $message = '')
    {
        $this->message = $message;
        parent::__construct($className . '::' . $methodName);
    }

    /**
     * @throws Exception
     */
    protected function runTest()
    {
        $this->markTestIncomplete($this->message);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns a string representation of the test case.
     *
     * @return string
     */
    public function toString()
    {
        return $this->getName();
    }
}
