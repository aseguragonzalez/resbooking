<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Application\ProcessPendingTasks;

use Framework\BackgroundTasks\Domain\Repositories\TaskRepository;
use Framework\BackgroundTasks\Domain\TaskBus;
use Framework\BackgroundTasks\Domain\TransactionRunner;
use Psr\Log\LoggerInterface;

final readonly class ProcessPendingTasksHandler implements ProcessPendingTasks
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskBus $taskBus,
        private TransactionRunner $transactionRunner,
        private LoggerInterface $logger,
    ) {
    }

    public function execute(ProcessPendingTasksCommand $command): void
    {
        $tasks = $this->taskRepository->findPending($command->limit);

        foreach ($tasks as $task) {
            try {
                $this->transactionRunner->runInTransaction(function () use ($task): void {
                    $this->taskRepository->save($task->markAsProcessed());
                    $this->taskBus->dispatch($task);
                });
            } catch (\Throwable $e) {
                $this->logger->error('Failed to process task', [
                    'taskId' => $task->id,
                    'taskType' => $task->taskType,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
