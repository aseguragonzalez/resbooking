<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Migrations\Domain;

use Framework\Migrations\Domain\Services\MigrationTestScope;
use Framework\Migrations\Domain\Services\RollbackExecutor;
use Framework\Migrations\Domain\Services\SchemaSnapshotExecutor;
use Framework\Migrations\Domain\Services\TestMigrationExecutor;
use PHPUnit\Framework\TestCase;

final class MigrationTestScopeTest extends TestCase
{
    public function testScopeExposesSnapshotExecutorTestExecutorAndRollbackExecutor(): void
    {
        $schemaSnapshotExecutor = $this->createStub(SchemaSnapshotExecutor::class);
        $testMigrationExecutor = $this->createStub(TestMigrationExecutor::class);
        $rollbackExecutor = $this->createStub(RollbackExecutor::class);

        $scope = new MigrationTestScope(
            schemaSnapshotExecutor: $schemaSnapshotExecutor,
            testMigrationExecutor: $testMigrationExecutor,
            rollbackExecutor: $rollbackExecutor,
        );

        $this->assertSame($schemaSnapshotExecutor, $scope->schemaSnapshotExecutor);
        $this->assertSame($testMigrationExecutor, $scope->testMigrationExecutor);
        $this->assertSame($rollbackExecutor, $scope->rollbackExecutor);
    }
}
