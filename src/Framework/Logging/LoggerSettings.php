<?php

declare(strict_types=1);

namespace Framework\Logging;

final readonly class LoggerSettings
{
    public function __construct(
        public string $environment = 'local',
        public string $serviceName = 'my-app',
        public string $serviceVersion = '1.0.0',
        public string $logLevel = 'debug',
    ) {
    }
}
