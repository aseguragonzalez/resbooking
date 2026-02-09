<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Migrations\Infrastructure;

use Framework\Migrations\Infrastructure\ShellDatabaseBackupManager;
use Framework\Migrations\MigrationSettings;
use PHPUnit\Framework\TestCase;

final class ShellDatabaseBackupManagerTest extends TestCase
{
    public function testCreateTestDatabaseFromBackupThrowsWhenBackupFileDoesNotExist(): void
    {
        $settings = new MigrationSettings(
            host: 'localhost',
            database: 'reservations',
            user: 'user',
            password: 'pass',
        );
        $manager = new ShellDatabaseBackupManager($settings);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Backup file does not exist');

        $manager->createTestDatabaseFromBackup('/nonexistent/backup.sql');
    }
}
