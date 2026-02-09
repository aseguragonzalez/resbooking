<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Infrastructure;

use Framework\BackgroundTasks\Domain\TaskHandler;
use Framework\BackgroundTasks\Domain\TaskHandlerRegistry;

final class MapTaskHandlerRegistry implements TaskHandlerRegistry
{
    /**
     * @var array<string, TaskHandler>
     */
    private array $handlers = [];

    public function register(string $taskType, TaskHandler $handler): void
    {
        $this->handlers[$taskType] = $handler;
    }

    public function getHandler(string $taskType): ?TaskHandler
    {
        return $this->handlers[$taskType] ?? null;
    }
}
