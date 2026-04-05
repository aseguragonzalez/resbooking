<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Domain;

interface TaskHandler
{
    public function handle(Task $task): void;
}
