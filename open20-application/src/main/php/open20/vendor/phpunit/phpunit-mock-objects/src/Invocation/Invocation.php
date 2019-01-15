<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */
namespace PHPUnit\Framework\MockObject;

/**
 * Interface for invocations.
 */
interface Invocation
{
    /**
     * @return mixed Mocked return value.
     */
    public function generateReturnValue();

    public function getClassName(): string;

    public function getMethodName(): string;

    public function getParameters(): array;

    public function getReturnType(): string;

    public function isReturnTypeNullable(): bool;
}
