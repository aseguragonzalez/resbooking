<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Application;

final class TestMigrationCommand
{
    public function __construct(
        public readonly string $migrationName,
        public readonly string $basePath,
    ) {
    }
}
