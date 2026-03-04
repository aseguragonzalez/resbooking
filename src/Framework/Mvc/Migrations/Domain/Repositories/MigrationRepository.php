<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations\Domain\Repositories;

use Framework\Mvc\Migrations\Domain\Entities\Migration;

interface MigrationRepository
{
    public function save(Migration $migration): void;

    /**
     * @return array<Migration>
     */
    public function getMigrations(): array;
}
