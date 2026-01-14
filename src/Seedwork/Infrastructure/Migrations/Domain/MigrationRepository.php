<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain;

interface MigrationRepository
{
    public function save(Migration $migration): void;

    /**
     * @return array<Migration>
     */
    public function getMigrations(): array;
}
