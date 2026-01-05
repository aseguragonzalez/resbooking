<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use Seedwork\Infrastructure\Mvc\ErrorMapping;

final readonly class ErrorSettings
{
    /**
     * @param array<class-string<\Throwable>, ErrorMapping> $errorsMapping
     */
    public function __construct(public array $errorsMapping, public ErrorMapping $errorsMappingDefaultValue)
    {
    }
}
