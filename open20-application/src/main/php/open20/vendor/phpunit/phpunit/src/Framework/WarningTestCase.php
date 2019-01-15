<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework;

/**
 * A warning.
 */
class WarningTestCase extends TestCase
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
     * @param string $message
     */
    public function __construct($message = '')
    {
        $this->message = $message;
        parent::__construct('Warning');
    }

    /**
     * @throws Exception
     */
    protected function runTest()
    {
        throw new Warning($this->message);
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
        return 'Warning';
    }
}
