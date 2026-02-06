<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\BackgroundTasks\Infrastructure;

use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Infrastructure\SqlTaskRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SqlTaskRepositoryTest extends TestCase
{
    private PDO&MockObject $pdo;
    private PDOStatement&MockObject $statement;
    private SqlTaskRepository $repository;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->statement = $this->createMock(PDOStatement::class);
        $this->repository = new SqlTaskRepository($this->pdo);
    }

    public function testSaveInsertsTaskWithTypeAndArguments(): void
    {
        $task = Task::new('send_notification', ['user_id' => '123', 'template' => 'welcome']);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO background_tasks'))
            ->willReturn($this->statement);

        $this->statement->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (array $params): bool {
                $createdAt = $params['created_at'] ?? null;
                return isset(
                    $params['id'],
                    $params['task_type'],
                    $params['arguments'],
                    $params['created_at'],
                    $params['processed']
                )
                    && array_key_exists('processed_at', $params)
                    && $params['task_type'] === 'send_notification'
                    && $params['arguments'] === '{"user_id":"123","template":"welcome"}'
                    && $params['processed'] === 0
                    && $params['processed_at'] === null
                    && is_string($params['id'])
                    && strlen($params['id']) > 0
                    && is_string($createdAt)
                    && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $createdAt) === 1;
            }))
            ->willReturn(true);

        $this->repository->save($task);
    }

    public function testSaveWithEmptyArgumentsInsertsEmptyJson(): void
    {
        $task = Task::new('no_op', []);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->statement);

        $this->statement->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (array $params): bool {
                return $params['arguments'] === '{}'
                    && $params['task_type'] === 'no_op'
                    && $params['processed'] === 0
                    && $params['processed_at'] === null;
            }))
            ->willReturn(true);

        $this->repository->save($task);
    }

    public function testSaveWithScalarArgumentValues(): void
    {
        $arguments = [
            'name' => 'test',
            'count' => 42,
            'ratio' => 3.14,
            'active' => true,
            'nullable' => null,
        ];
        $task = Task::new('scalar_task', $arguments);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->statement);

        $this->statement->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (array $params) use ($arguments): bool {
                $args = $params['arguments'];
                if (!is_string($args)) {
                    return false;
                }
                $decoded = json_decode($args, true);

                return $decoded === $arguments
                    && $params['processed'] === 0
                    && $params['processed_at'] === null;
            }))
            ->willReturn(true);

        $this->repository->save($task);
    }

    public function testSaveWithNestedArgumentsStoresCorrectJson(): void
    {
        $arguments = ['a' => ['b' => 1, 'c' => ['d' => 'x']]];
        $task = Task::new('nested_task', $arguments);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->statement);

        $this->statement->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (array $params) use ($arguments): bool {
                $args = $params['arguments'];
                if (!is_string($args)) {
                    return false;
                }
                $decoded = json_decode($args, true);

                return $decoded === $arguments
                    && $params['task_type'] === 'nested_task'
                    && $params['processed'] === 0
                    && $params['processed_at'] === null;
            }))
            ->willReturn(true);

        $this->repository->save($task);
    }
}
