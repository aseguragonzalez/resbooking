<?php

declare(strict_types=1);

namespace Framework\Mvc;

use Framework\Mvc\ErrorMapping;

final readonly class ErrorSettings
{
    /**
     * @param array<class-string<\Throwable>, ErrorMapping> $errorsMapping
     */
    public function __construct(public array $errorsMapping, public ErrorMapping $errorsMappingDefaultValue)
    {
    }
}
