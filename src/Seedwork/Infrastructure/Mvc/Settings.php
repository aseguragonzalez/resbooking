<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

final class Settings
{
    public readonly string $i18nPath;
    public readonly string $viewPath;

    public function __construct(
        public readonly string $basePath,
        public readonly string $defaultLocale = 'en-en',
        public readonly string $environment = 'local',
        string $i18nPath = '/assets/i18n',
        public readonly string $serviceName = 'my-app',
        public readonly string $serviceVersion = '1.0.0',
        string $viewPath = '/Views',
    ) {
        $this->i18nPath = $this->basePath . $i18nPath;
        $this->viewPath = $this->basePath . $viewPath;
    }
}
