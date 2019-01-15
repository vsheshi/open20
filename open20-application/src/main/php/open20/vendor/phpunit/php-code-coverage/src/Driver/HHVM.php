<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace SebastianBergmann\CodeCoverage\Driver;

/**
 * Driver for HHVM's code coverage functionality.
 *
 * @codeCoverageIgnore
 */
class HHVM extends Xdebug
{
    /**
     * Start collection of code coverage information.
     *
     * @param bool $determineUnusedAndDead
     */
    public function start($determineUnusedAndDead = true)
    {
        \xdebug_start_code_coverage();
    }
}
