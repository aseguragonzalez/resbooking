<?php

declare(strict_types=1);

namespace Framework\Migrations\Domain\Services;

use Framework\Migrations\Domain\Entities\Migration;

interface MigrationFileManager
{
    /**
     * @return array<Migration>
     */
    public function getMigrations(string $basePath): array;

    public function getMigrationByName(string $basePath, string $migrationName): ?Migration;
}
