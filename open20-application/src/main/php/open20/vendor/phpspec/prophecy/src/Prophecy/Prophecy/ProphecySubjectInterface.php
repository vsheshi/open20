<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Prophecy;

/**
 * Controllable doubles interface.
 *
 */
interface ProphecySubjectInterface
{
    /**
     * Sets subject prophecy.
     *
     * @param ProphecyInterface $prophecy
     */
    public function setProphecy(ProphecyInterface $prophecy);

    /**
     * Returns subject prophecy.
     *
     * @return ProphecyInterface
     */
    public function getProphecy();
}
