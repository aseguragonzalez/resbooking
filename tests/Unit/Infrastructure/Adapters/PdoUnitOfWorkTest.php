<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Adapters;

use Infrastructure\Adapters\PdoUnitOfWork;
use PHPUnit\Framework\TestCase;
use PDO;

final class PdoUnitOfWorkTest extends TestCase
{
    public function testCreateSessionBeginsTransaction(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->never())->method('commit');
        $pdo->expects($this->never())->method('rollBack');

        $uow = new PdoUnitOfWork($pdo);
        $uow->createSession();
    }

    public function testCommitCallsPdoCommit(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())->method('commit');

        $uow = new PdoUnitOfWork($pdo);
        $uow->commit();
    }

    public function testRollbackCallsPdoRollBack(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())->method('rollBack');

        $uow = new PdoUnitOfWork($pdo);
        $uow->rollback();
    }
}
