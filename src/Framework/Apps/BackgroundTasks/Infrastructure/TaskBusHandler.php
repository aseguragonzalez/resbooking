<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Infrastructure;

use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Domain\TaskBus;
use Framework\BackgroundTasks\Domain\TaskHandlerRegistry;

final readonly class TaskBusHandler implements TaskBus
{
    public function __construct(private TaskHandlerRegistry $registry)
    {
    }

    public function dispatch(Task $task): void
    {
        $handler = $this->registry->getHandler($task->taskType);
        if ($handler === null) {
            return;
        }
        $handler->handle($task);
    }
}
