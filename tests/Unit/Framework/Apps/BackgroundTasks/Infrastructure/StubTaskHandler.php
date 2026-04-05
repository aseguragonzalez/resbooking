<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Apps\BackgroundTasks\Infrastructure;

use Framework\Apps\BackgroundTasks\Domain\Task;
use Framework\Apps\BackgroundTasks\Domain\TaskHandler;

final class StubTaskHandler implements TaskHandler
{
    public function handle(Task $task): void
    {
    }
}
