<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks\Domain;

interface TaskHandlerRegistry
{
    public function register(string $taskType, TaskHandler $handler): void;
    public function getHandler(string $taskType): ?TaskHandler;
}
