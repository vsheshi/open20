<?php
/**
 *
 *
 */

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Type;

/**
 * Value Object representing the 'self' type.
 *
 * Self, as a Type, represents the class in which the associated element was defined.
 */
final class Self_ implements Type
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     *
     * @return string
     */
    public function __toString()
    {
        return 'self';
    }
}
