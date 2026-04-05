<?php

declare(strict_types=1);

namespace Framework\Apps\BackgroundTasks\Application\RegisterTask;

interface RegisterTask
{
    public function execute(RegisterTaskCommand $command): void;
}
