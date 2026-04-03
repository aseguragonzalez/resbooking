<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations\Application;

interface TestMigration
{
    public function execute(TestMigrationCommand $command): void;
}
