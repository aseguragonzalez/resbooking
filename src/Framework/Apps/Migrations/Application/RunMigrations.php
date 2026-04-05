<?php

declare(strict_types=1);

namespace Framework\Migrations\Application;

interface RunMigrations
{
    public function execute(string $basePath): void;
}
