<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks\Application\RegisterTask;

use Framework\Mvc\BackgroundTasks\Domain\Repositories\TaskRepository;
use Framework\Mvc\BackgroundTasks\Domain\Task;

final readonly class RegisterTaskHandler implements RegisterTask
{
    public function __construct(private TaskRepository $taskRepository)
    {
    }

    public function execute(RegisterTaskCommand $command): void
    {
        $task = Task::new($command->taskType, $command->arguments);

        $this->taskRepository->save($task);
    }
}
