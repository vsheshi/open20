<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PHPUnit\Util;

/**
 * Filesystem helpers.
 */
class Filesystem
{
    /**
     * @var array
     */
    protected static $buffer = [];

    /**
     * Maps class names to source file names:
     *   - PEAR CS:   Foo_Bar_Baz -> Foo/Bar/Baz.php
     *   - Namespace: Foo\Bar\Baz -> Foo/Bar/Baz.php
     *
     * @param string $className
     *
     * @return string
     */
    public static function classNameToFilename($className)
    {
        return \str_replace(
            ['_', '\\'],
            DIRECTORY_SEPARATOR,
            $className
        ) . '.php';
    }
}
