<?php
/**
 *
 *
 */

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Type;

/**
 * Value Object representing the 'scalar' pseudo-type, which is either a string, integer, float or boolean.
 */
final class Scalar implements Type
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     *
     * @return string
     */
    public function __toString()
    {
        return 'scalar';
    }
}
