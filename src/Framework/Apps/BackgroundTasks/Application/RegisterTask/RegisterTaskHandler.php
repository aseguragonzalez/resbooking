<?php

declare(strict_types=1);

namespace Framework\Apps\BackgroundTasks\Application\RegisterTask;

use Framework\Apps\BackgroundTasks\Domain\Repositories\TaskRepository;
use Framework\Apps\BackgroundTasks\Domain\Task;

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
