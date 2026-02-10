<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks\Settings;

final readonly class ChallengeEmailSettings
{
    public function __construct(
        public string $templateBasePath,
        public string $host,
        public int $port,
        public string $username,
        public string $password,
        public string $encryption,
        public string $fromAddress,
        public string $fromName,
        public string $appBaseUrl,
    ) {
    }
}
