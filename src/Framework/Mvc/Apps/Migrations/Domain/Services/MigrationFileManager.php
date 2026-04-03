<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations\Domain\Services;

use Framework\Mvc\Migrations\Domain\Entities\Migration;

interface MigrationFileManager
{
    /**
     * @return array<Migration>
     */
    public function getMigrations(string $basePath): array;

    public function getMigrationByName(string $basePath, string $migrationName): ?Migration;
}
