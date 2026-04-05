<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Apps\BackgroundTasks\Infrastructure;

use Framework\BackgroundTasks\Domain\Task;
use Framework\BackgroundTasks\Domain\TaskHandler;

final class StubTaskHandler implements TaskHandler
{
    public function handle(Task $task): void
    {
    }
}
