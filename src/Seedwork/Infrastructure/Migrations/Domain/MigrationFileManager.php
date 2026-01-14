<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain;

use Seedwork\Infrastructure\Migrations\Domain\Migration;

interface MigrationFileManager
{
    /**
     * @return array<Migration>
     */
    public function getMigrations(string $basePath): array;
}
