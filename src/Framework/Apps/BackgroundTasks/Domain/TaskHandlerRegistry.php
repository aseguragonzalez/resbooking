<?php

declare(strict_types=1);

namespace Framework\Apps\BackgroundTasks\Domain;

interface TaskHandlerRegistry
{
    public function getHandler(string $taskType): ?TaskHandler;
}
