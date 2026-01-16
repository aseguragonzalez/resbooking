<?php

declare(strict_types=1);

namespace Framework\Logging;

interface Logger
{
    public function critical(string $message, \Exception|\Throwable $error): void;

    public function debug(string $message): void;

    public function error(string $message, \Exception|\Throwable $error): void;

    public function info(string $message): void;

    public function warning(string $message, \Exception|\Throwable $error): void;
}
