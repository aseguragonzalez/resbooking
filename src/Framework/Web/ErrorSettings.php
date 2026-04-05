<?php

declare(strict_types=1);

namespace Framework;

final readonly class ErrorSettings
{
    /**
     * @param array<class-string<\Throwable>, ErrorMapping> $errorsMapping Per throwable class;
     *   {@see \Framework\Middlewares\ErrorHandling} matches the thrown instance's class and its parents
     * @param ErrorMapping $errorsMappingDefaultValue Used when no entry matches the exception hierarchy
     */
    public function __construct(public array $errorsMapping, public ErrorMapping $errorsMappingDefaultValue)
    {
    }
}
