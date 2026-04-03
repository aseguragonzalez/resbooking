<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks\Domain\Repositories;

use Framework\Mvc\BackgroundTasks\Domain\Task;

interface TaskRepository
{
    public function save(Task $task): void;

    /**
     * @return array<Task>
     */
    public function findPending(int $limit): array;
}
