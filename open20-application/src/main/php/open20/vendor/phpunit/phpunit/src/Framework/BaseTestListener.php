<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework;

/**
 * Implementation of the TestListener interface that does not do anything.
 *
 * @deprecated Use TestListenerDefaultImplementation trait instead
 */
abstract class BaseTestListener implements TestListener
{
    use TestListenerDefaultImplementation;
}
