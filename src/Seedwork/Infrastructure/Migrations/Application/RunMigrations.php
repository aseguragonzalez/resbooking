<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Application;

interface RunMigrations
{
    public function execute(string $basePath): void;
}
