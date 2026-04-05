<?php

declare(strict_types=1);

namespace Framework\Apps\BackgroundTasks\Application\ProcessPendingTasks;

interface ProcessPendingTasks
{
    public function execute(ProcessPendingTasksCommand $command): void;
}
