<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework;

/**
 * Extension to PHPUnit\Framework\AssertionFailedError to mark the special
 * case of a test that unintentionally covers code.
 */
class UnintentionallyCoveredCodeError extends RiskyTestError
{
}
