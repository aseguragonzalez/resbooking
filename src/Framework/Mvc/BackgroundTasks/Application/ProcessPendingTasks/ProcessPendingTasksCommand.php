<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks\Application\ProcessPendingTasks;

final readonly class ProcessPendingTasksCommand
{
    public function __construct(
        public int $limit = 100,
    ) {
    }
}
