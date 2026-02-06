<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\BackgroundTasks\Application\RegisterTask;

use Framework\BackgroundTasks\Application\RegisterTask\RegisterTask;
use Framework\BackgroundTasks\Application\RegisterTask\RegisterTaskCommand;
use Framework\BackgroundTasks\Application\RegisterTask\RegisterTaskHandler;
use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Domain\Repositories\TaskRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RegisterTaskTest extends TestCase
{
    private MockObject&TaskRepository $taskRepository;
    private RegisterTask $service;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->service = new RegisterTaskHandler($this->taskRepository);
    }

    public function testExecuteSavesTaskWithCommandTypeAndArguments(): void
    {
        $command = new RegisterTaskCommand('send_email', ['to' => 'user@example.com']);

        $this->taskRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Task $task): bool {
                return $task->taskType === 'send_email'
                    && $task->arguments === ['to' => 'user@example.com'];
            }));

        $this->service->execute($command);
    }

    public function testExecuteSavesTaskWithNestedArguments(): void
    {
        $arguments = ['a' => ['b' => 1, 'c' => ['d' => 'x']]];
        $command = new RegisterTaskCommand('nested_task', $arguments);

        $this->taskRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Task $task) use ($arguments): bool {
                return $task->taskType === 'nested_task'
                    && $task->arguments === $arguments;
            }));

        $this->service->execute($command);
    }
}
