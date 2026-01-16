<?php

declare(strict_types=1);

namespace Framework\Migrations\Domain\Services;

interface DatabaseBackupManager
{
    public function backup(): string;

    public function restore(string $backupFilePath): void;
}
