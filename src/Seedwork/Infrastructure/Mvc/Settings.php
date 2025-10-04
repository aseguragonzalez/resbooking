<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

use Seedwork\Infrastructure\Mvc\ErrorMapping;

final class Settings
{
    public readonly string $i18nPath;
    public readonly string $viewPath;

    /**
     * @param array<string> $languages
     * @param array<class-string<\Throwable>, ErrorMapping> $errorsMapping
     */
    public function __construct(
        public readonly string $basePath,
        public readonly ErrorMapping $errorsMappingDefaultValue = new ErrorMapping(
            statusCode: 500,
            templateName: 'Shared/500',
            pageTitle: '{{internalServerError.title}}'
        ),
        public readonly string $environment = 'local',
        string $i18nPath = '/assets/i18n',
        public readonly array $languages = ['en'],
        public readonly string $languageCookieName = 'language',
        public readonly string $languageDefaultValue = 'en',
        public readonly string $languageSetUrl = '/set-language',
        public readonly string $serviceName = 'my-app',
        public readonly string $serviceVersion = '1.0.0',
        string $viewPath = '/Views',
        public readonly array $errorsMapping = [],
        public readonly string $authCookieName = 'auth',
        public readonly string $authLoginUrl = '/accounts/sign-in',
        public readonly string $authLogoutUrl = '/accounts/sign-out',
    ) {
        $this->i18nPath = $this->basePath . $i18nPath;
        $this->viewPath = $this->basePath . $viewPath;
    }
}
