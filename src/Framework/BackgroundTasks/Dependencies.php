<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks;

use DI\Container;
use Framework\BackgroundTasks\Application\RegisterTask\RegisterTask;
use Framework\BackgroundTasks\Application\RegisterTask\RegisterTaskHandler;
use Framework\BackgroundTasks\Domain\Repositories\TaskRepository;
use Framework\BackgroundTasks\Infrastructure\SqlTaskRepository;

final class Dependencies
{
    public static function configure(Container $container): void
    {
        $container->set(TaskRepository::class, $container->get(SqlTaskRepository::class));
        $container->set(RegisterTask::class, $container->get(RegisterTaskHandler::class));
    }
}
