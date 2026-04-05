<?php

declare(strict_types=1);

namespace Framework\Cli\Commands;

interface Command
{
    public function getName(): string;

    public function getDescription(): string;

    /**
     * @param array<string> $args
     */
    public function execute(array $args): int;
}
