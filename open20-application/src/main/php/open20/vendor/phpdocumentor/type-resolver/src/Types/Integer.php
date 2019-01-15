<?php
/**
 *
 *
 */

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Type;

final class Integer implements Type
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     *
     * @return string
     */
    public function __toString()
    {
        return 'int';
    }
}
