<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace SebastianBergmann\CodeCoverage;

use SebastianBergmann\Version as VersionId;

class Version
{
    private static $version;

    /**
     * @return string
     */
    public static function id()
    {
        if (self::$version === null) {
            $version       = new VersionId('5.3.0', \dirname(__DIR__));
            self::$version = $version->getVersion();
        }

        return self::$version;
    }
}
