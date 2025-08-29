<?php

declare(strict_types=1);

namespace Seedwork\Application\Logging;

interface Logger
{
    public function critical(string $message, \Exception $error): void;

    public function debug(string $message): void;

    public function error(string $message, \Exception $error): void;

    public function info(string $message): void;

    public function warning(string $message, \Exception $error): void;
}
