<?php

declare(strict_types=1);

namespace Framework\Apps\BackgroundTasks;

use Framework\Container\MutableContainer;
use Framework\Apps\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasks;
use Framework\Apps\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasksHandler;
use Framework\Apps\BackgroundTasks\Application\RegisterTask\RegisterTask;
use Framework\Apps\BackgroundTasks\Application\RegisterTask\RegisterTaskHandler;
use Framework\Apps\BackgroundTasks\Domain\Repositories\TaskRepository;
use Framework\Apps\BackgroundTasks\Domain\TaskBus;
use Framework\Apps\BackgroundTasks\Domain\TaskHandlerRegistry;
use Framework\Apps\BackgroundTasks\Domain\TransactionRunner;
use Framework\Apps\BackgroundTasks\Infrastructure\ContainerTaskHandlerRegistry;
use Framework\Apps\BackgroundTasks\Infrastructure\PdoTransactionRunner;
use Framework\Apps\BackgroundTasks\Infrastructure\SqlTaskRepository;
use Framework\Apps\BackgroundTasks\Infrastructure\TaskBusHandler;

final class Dependencies
{
    /**
     * Registers framework BackgroundTasks bindings. Call after the composition root has set
     * {@see \PDO} and {@see TaskHandlerClassMap} on the container (plus any app-specific services).
     */
    public static function configure(MutableContainer $container): void
    {
        $container->set(TaskHandlerRegistry::class, $container->get(ContainerTaskHandlerRegistry::class));
        $container->set(TaskRepository::class, $container->get(SqlTaskRepository::class));
        $container->set(TransactionRunner::class, $container->get(PdoTransactionRunner::class));
        $container->set(RegisterTask::class, $container->get(RegisterTaskHandler::class));
        $container->set(TaskBus::class, $container->get(TaskBusHandler::class));
        $container->set(ProcessPendingTasks::class, $container->get(ProcessPendingTasksHandler::class));
    }
}
