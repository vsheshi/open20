<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception\Doubler;

class InterfaceNotFoundException extends ClassNotFoundException
{
    public function getInterfaceName()
    {
        return $this->getClassname();
    }
}
