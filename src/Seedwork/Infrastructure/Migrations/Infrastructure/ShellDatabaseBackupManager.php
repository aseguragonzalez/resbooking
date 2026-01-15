<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Infrastructure;

use Seedwork\Application\Logging\Logger;
use Seedwork\Infrastructure\Migrations\Domain\Services\DatabaseBackupManager;

final readonly class ShellDatabaseBackupManager implements DatabaseBackupManager
{
    public function __construct(
        private MigrationSettings $settings,
        private Logger $logger,
    ) {
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
            $errorMessage = implode("\n", $output);
            $exception = new \RuntimeException("Failed to create database backup: {$errorMessage}");
            $this->logger->error("Failed to create database backup: {$errorMessage}", $exception);
            throw $exception;
        }

        $this->logger->info("Database backup created: {$backupFile}");
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
            $errorMessage = implode("\n", $output);
            $exception = new \RuntimeException("Failed to restore database from backup: {$errorMessage}");
            $this->logger->error("Failed to restore database from backup: {$errorMessage}", $exception);
            throw $exception;
        }

        $this->logger->info("Database restored from backup: {$backupFilePath}");

        // Clean up backup file
        if (unlink($backupFilePath)) {
            $this->logger->info("Backup file deleted: {$backupFilePath}");
        } else {
            $this->logger->info("Failed to delete backup file: {$backupFilePath}");
        }
    }
}
