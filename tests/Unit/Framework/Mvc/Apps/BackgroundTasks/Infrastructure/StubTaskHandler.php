<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Apps\BackgroundTasks\Infrastructure;

use Framework\Mvc\BackgroundTasks\Domain\Task;
use Framework\Mvc\BackgroundTasks\Domain\TaskHandler;

final class StubTaskHandler implements TaskHandler
{
    public function handle(Task $task): void
    {
    }
}
