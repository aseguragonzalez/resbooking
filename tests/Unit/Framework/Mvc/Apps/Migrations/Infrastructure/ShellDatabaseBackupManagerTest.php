<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Migrations\Infrastructure;

use Framework\Mvc\Migrations\Infrastructure\ShellDatabaseBackupManager;
use Framework\Mvc\Migrations\MigrationsMysqlConnection;
use PHPUnit\Framework\TestCase;

final class ShellDatabaseBackupManagerTest extends TestCase
{
    public function testCreateTestDatabaseFromBackupThrowsWhenBackupFileDoesNotExist(): void
    {
        $mysql = new MigrationsMysqlConnection(
            host: 'localhost',
            database: 'reservations',
            user: 'user',
            password: 'pass',
        );
        $manager = new ShellDatabaseBackupManager($mysql);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('#^Backup file does not exist: /nonexistent/backup\.sql$#');

        $manager->createTestDatabaseFromBackup('/nonexistent/backup.sql');
    }
}
