<?php

declare(strict_types=1);

namespace Framework\Migrations\Infrastructure;

use Framework\Migrations\Domain\Services\DatabaseBackupManager;
use Framework\Migrations\MigrationSettings;

final readonly class ShellDatabaseBackupManager implements DatabaseBackupManager
{
    public function __construct(private MigrationSettings $settings)
    {
    }

    public function backup(): string
    {
        $backupFile = sys_get_temp_dir() . '/migration_test_' . uniqid() . '.sql';
        $command = sprintf(
            'mysqldump -h %s -u %s -p%s --no-data %s > %s 2>&1',
            escapeshellarg($this->settings->host),
            escapeshellarg($this->settings->user),
            escapeshellarg($this->settings->password),
            escapeshellarg($this->settings->database),
            escapeshellarg($backupFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException("Failed to create database backup: " . implode("\n", $output));
        }

        return $backupFile;
    }

    public function restore(string $backupFilePath): void
    {
        if (!file_exists($backupFilePath)) {
            throw new \RuntimeException("Backup file does not exist: {$backupFilePath}");
        }

        $command = sprintf(
            'mysql -h %s -u %s -p%s %s < %s 2>&1',
            escapeshellarg($this->settings->host),
            escapeshellarg($this->settings->user),
            escapeshellarg($this->settings->password),
            escapeshellarg($this->settings->database),
            escapeshellarg($backupFilePath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException("Failed to restore database from backup: " . implode("\n", $output));
        }

        unlink($backupFilePath);
    }
}
