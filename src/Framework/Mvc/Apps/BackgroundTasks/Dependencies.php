<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks;

use Framework\Mvc\Container\MutableContainer;
use Framework\Mvc\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasks;
use Framework\Mvc\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasksHandler;
use Framework\Mvc\BackgroundTasks\Application\RegisterTask\RegisterTask;
use Framework\Mvc\BackgroundTasks\Application\RegisterTask\RegisterTaskHandler;
use Framework\Mvc\BackgroundTasks\Domain\Repositories\TaskRepository;
use Framework\Mvc\BackgroundTasks\Domain\TaskBus;
use Framework\Mvc\BackgroundTasks\Domain\TaskHandlerRegistry;
use Framework\Mvc\BackgroundTasks\Domain\TransactionRunner;
use Framework\Mvc\BackgroundTasks\Infrastructure\ContainerTaskHandlerRegistry;
use Framework\Mvc\BackgroundTasks\Infrastructure\PdoTransactionRunner;
use Framework\Mvc\BackgroundTasks\Infrastructure\SqlTaskRepository;
use Framework\Mvc\BackgroundTasks\Infrastructure\TaskBusHandler;

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
