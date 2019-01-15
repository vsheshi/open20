<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Util\PHP;

use PHPUnit\Framework\Exception;

/**
 * Windows utility for PHP sub-processes.
 *
 * Reading from STDOUT or STDERR hangs forever on Windows if the output is
 * too large.
 *
 */
class WindowsPhpProcess extends DefaultPhpProcess
{
    protected $useTempFile = true;

    protected function getHandles()
    {
        if (false === $stdout_handle = \tmpfile()) {
            throw new Exception(
                'A temporary file could not be created; verify that your TEMP environment variable is writable'
            );
        }

        return [
            1 => $stdout_handle
        ];
    }

    public function getCommand(array $settings, $file = null)
    {
        return '"' . parent::getCommand($settings, $file) . '"';
    }
}
