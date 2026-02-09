<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Domain;

interface TaskBus
{
    public function dispatch(Task $task): void;
}
