<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks\Application\ProcessPendingTasks;

interface ProcessPendingTasks
{
    public function execute(ProcessPendingTasksCommand $command): void;
}
