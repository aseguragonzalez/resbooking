<?php

declare(strict_types=1);

namespace Infrastructure\Ports\BackgroundTasks;

use Framework\BackgroundTasks\BackgroundTasksApp as FrameworkBackgroundTasksApp;

final class BackgroundTasksApp extends FrameworkBackgroundTasksApp
{
    /**
     * @return array<string, string>
     */
    protected function getHandlerMap(): array
    {
        return [];
    }
}
