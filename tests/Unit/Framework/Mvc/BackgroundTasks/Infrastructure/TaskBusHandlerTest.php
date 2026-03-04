<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\BackgroundTasks\Infrastructure;

use Framework\Mvc\BackgroundTasks\Domain\Task;
use Framework\Mvc\BackgroundTasks\Domain\TaskHandler;
use Framework\Mvc\BackgroundTasks\Domain\TaskHandlerRegistry;
use Framework\Mvc\BackgroundTasks\Infrastructure\TaskBusHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TaskBusHandlerTest extends TestCase
{
    private TaskHandlerRegistry&MockObject $registry;
    private TaskBusHandler $bus;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(TaskHandlerRegistry::class);
        $this->bus = new TaskBusHandler($this->registry);
    }

    public function testDispatchCallsHandlerWithTask(): void
    {
        $task = Task::new('send_email', ['to' => 'user@example.com']);
        $handler = $this->createMock(TaskHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($task));

        $this->registry->expects($this->once())
            ->method('getHandler')
            ->with('send_email')
            ->willReturn($handler);

        $this->bus->dispatch($task);
    }

    public function testDispatchDoesNothingWhenNoHandlerRegistered(): void
    {
        $task = Task::new('unknown_type', []);

        $this->registry->expects($this->once())
            ->method('getHandler')
            ->with('unknown_type')
            ->willReturn(null);

        $this->bus->dispatch($task);
    }
}
