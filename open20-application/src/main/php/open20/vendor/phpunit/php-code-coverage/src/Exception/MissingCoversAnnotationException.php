<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace SebastianBergmann\CodeCoverage;

/**
 * Exception that is raised when @covers must be used but is not.
 */
class MissingCoversAnnotationException extends RuntimeException
{
}
