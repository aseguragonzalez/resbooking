<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Application;

interface TestMigration
{
    public function execute(TestMigrationCommand $command): void;
}
