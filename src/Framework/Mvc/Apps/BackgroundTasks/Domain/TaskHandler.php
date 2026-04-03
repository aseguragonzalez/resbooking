<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks\Domain;

interface TaskHandler
{
    public function handle(Task $task): void;
}
