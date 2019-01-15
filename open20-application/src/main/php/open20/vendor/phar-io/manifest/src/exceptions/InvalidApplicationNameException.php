<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class InvalidApplicationNameException extends \InvalidArgumentException implements Exception {
    const NotAString    = 1;
    const InvalidFormat = 2;
}
