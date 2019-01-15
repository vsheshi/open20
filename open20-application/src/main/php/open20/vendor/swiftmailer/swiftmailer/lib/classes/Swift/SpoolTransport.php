<?php

/*
 * (l) 2009 Fabien Potencier <fabien.potencier@gmail.com>
 *
 */

/**
 * Stores Messages in a queue.
 *
 */
class Swift_SpoolTransport extends Swift_Transport_SpoolTransport
{
    /**
     * Create a new SpoolTransport.
     *
     * @param Swift_Spool $spool
     */
    public function __construct(Swift_Spool $spool)
    {
        $arguments = Swift_DependencyContainer::getInstance()
            ->createDependenciesFor('transport.spool');

        $arguments[] = $spool;

        call_user_func_array(
            array($this, 'Swift_Transport_SpoolTransport::__construct'),
            $arguments
        );
    }
}
