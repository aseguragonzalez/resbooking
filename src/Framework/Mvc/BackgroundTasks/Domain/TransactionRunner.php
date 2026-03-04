<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks\Domain;

interface TransactionRunner
{
    public function runInTransaction(\Closure $operation): void;
}
