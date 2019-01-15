<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PHPUnit\Util;

/**
 * Utility class for textual type (and value) representation.
 */
class Type
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isType($type)
    {
        return \in_array(
            $type,
            [
                'numeric',
                'integer',
                'int',
                'float',
                'string',
                'boolean',
                'bool',
                'null',
                'array',
                'object',
                'resource',
                'scalar'
            ]
        );
    }
}
