<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\BackgroundTasks\Infrastructure;

use Framework\BackgroundTasks\Infrastructure\PdoTransactionRunner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PDO;

final class PdoTransactionRunnerTest extends TestCase
{
    private PDO&MockObject $pdo;
    private PdoTransactionRunner $runner;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->runner = new PdoTransactionRunner($this->pdo);
    }

    public function testRunInTransactionCommitsOnSuccess(): void
    {
        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->once())->method('commit');
        $this->pdo->expects($this->never())->method('rollBack');

        $called = false;
        $this->runner->runInTransaction(function () use (&$called): void {
            $called = true;
        });

        $this->assertTrue($called);
    }

    public function testRunInTransactionRollsBackAndRethrowsOnException(): void
    {
        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->never())->method('commit');
        $this->pdo->expects($this->once())->method('rollBack');

        $exception = new \RuntimeException('Handler failed');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Handler failed');

        $this->runner->runInTransaction(function () use ($exception): void {
            throw $exception;
        });
    }
}
