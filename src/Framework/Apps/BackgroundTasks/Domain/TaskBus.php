<?php

declare(strict_types=1);

namespace Framework\Apps\BackgroundTasks\Domain;

interface TaskBus
{
    public function dispatch(Task $task): void;
}
