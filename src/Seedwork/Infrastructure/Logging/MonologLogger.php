<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Logging;

use Monolog\Logger as MonoLogger;
use Seedwork\Application\Logging\Logger;

final class MonologLogger implements Logger
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(private readonly MonoLogger $logger, private readonly array $context = [])
    {
    }

    public function critical(string $message, \Exception|\Throwable $exception): void
    {
        $this->logger->critical($message, array_merge($this->context, ['exception' => $exception]));
    }

    public function debug(string $message): void
    {
        $this->logger->debug($message, $this->context);
    }

    public function error(string $message, \Exception|\Throwable $exception): void
    {
        $this->logger->error($message, array_merge($this->context, ['exception' => $exception]));
    }

    public function info(string $message): void
    {
        $this->logger->info($message, $this->context);
    }

    public function warning(string $message, \Exception|\Throwable $exception): void
    {
        $this->logger->warning($message, array_merge($this->context, ['exception' => $exception]));
    }
}
