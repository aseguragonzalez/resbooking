<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Domain;

interface TaskHandlerRegistry
{
    public function getHandler(string $taskType): ?TaskHandler;
}
