<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PHPUnit\Framework\Error;

use PHPUnit\Framework\Exception;

/**
 * Wrapper for PHP errors.
 */
class Error extends Exception
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param string     $file
     * @param int        $line
     * @param \Exception $previous
     */
    public function __construct($message, $code, $file, $line, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->file = $file;
        $this->line = $line;
    }
}
