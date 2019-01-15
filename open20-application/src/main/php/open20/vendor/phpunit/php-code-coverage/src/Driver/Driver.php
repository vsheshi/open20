<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace SebastianBergmann\CodeCoverage\Driver;

/**
 * Interface for code coverage drivers.
 */
interface Driver
{
    /**
     * @var int
     *
     */
    const LINE_EXECUTED = 1;

    /**
     * @var int
     *
     */
    const LINE_NOT_EXECUTED = -1;

    /**
     * @var int
     *
     */
    const LINE_NOT_EXECUTABLE = -2;

    /**
     * Start collection of code coverage information.
     *
     * @param bool $determineUnusedAndDead
     */
    public function start($determineUnusedAndDead = true);

    /**
     * Stop collection of code coverage information.
     *
     * @return array
     */
    public function stop();
}
