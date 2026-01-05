<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use Seedwork\Infrastructure\Mvc\ErrorMapping;

class Settings
{
    public readonly string $viewPath;

    /**
     * @param array<class-string<\Throwable>, ErrorMapping> $errorsMapping
     */
    public function __construct(
        public readonly string $basePath,
        public readonly ErrorMapping $errorsMappingDefaultValue = new ErrorMapping(
            statusCode: 500,
            templateName: 'Shared/500',
            pageTitle: '{{internalServerError.title}}'
        ),
        string $viewPath = '/Views',
        public readonly array $errorsMapping = [],
    ) {
        $this->viewPath = $this->basePath . $viewPath;
    }
}
