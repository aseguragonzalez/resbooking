<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class ConfigurationService
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $serviceId,
        public int $minDiners,
        public int $maxDiners,
        public bool $reminders,
        public int $timespan,
        public int $timeFilter,
        public int $diners,
        public bool $advertising,
        public bool $preOrder,
    ) { }
}
