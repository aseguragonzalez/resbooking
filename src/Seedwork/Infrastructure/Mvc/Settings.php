<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

final class Settings
{
    public function __construct(
        public readonly string $basePath,
        public readonly string $i18nPath,
        public readonly string $viewPath,
        public readonly string $environment = 'local',
        public readonly string $serviceName = 'my-app',
        public readonly string $serviceVersion = '1.0.0',
        public readonly string $defaultLocale = 'en-en',
    ) {
    }
}
