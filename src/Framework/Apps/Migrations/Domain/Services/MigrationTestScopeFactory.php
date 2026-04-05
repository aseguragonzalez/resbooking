<?php

declare(strict_types=1);

namespace Framework\Apps\Migrations\Domain\Services;

interface MigrationTestScopeFactory
{
    public function createScope(string $databaseName): MigrationTestScope;
}
