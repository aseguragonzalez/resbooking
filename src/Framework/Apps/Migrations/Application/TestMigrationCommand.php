<?php

declare(strict_types=1);

namespace Framework\Apps\Migrations\Application;

final readonly class TestMigrationCommand
{
    public function __construct(
        public string $migrationName,
        public string $basePath,
    ) {
    }
}
