<?php

declare(strict_types=1);

namespace Framework\Apps\Migrations\Application;

interface RunMigrations
{
    public function execute(string $basePath): void;
}
