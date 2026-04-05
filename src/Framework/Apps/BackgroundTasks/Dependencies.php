<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks;

use Framework\Container\MutableContainer;
use Framework\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasks;
use Framework\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasksHandler;
use Framework\BackgroundTasks\Application\RegisterTask\RegisterTask;
use Framework\BackgroundTasks\Application\RegisterTask\RegisterTaskHandler;
use Framework\BackgroundTasks\Domain\Repositories\TaskRepository;
use Framework\BackgroundTasks\Domain\TaskBus;
use Framework\BackgroundTasks\Domain\TaskHandlerRegistry;
use Framework\BackgroundTasks\Domain\TransactionRunner;
use Framework\BackgroundTasks\Infrastructure\ContainerTaskHandlerRegistry;
use Framework\BackgroundTasks\Infrastructure\PdoTransactionRunner;
use Framework\BackgroundTasks\Infrastructure\SqlTaskRepository;
use Framework\BackgroundTasks\Infrastructure\TaskBusHandler;

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
