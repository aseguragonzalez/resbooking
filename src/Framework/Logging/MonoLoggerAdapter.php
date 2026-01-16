<?php

declare(strict_types=1);

namespace Framework\Logging;

use Psr\Log\LoggerInterface;

final readonly class MonoLoggerAdapter implements LoggerInterface
{
    /**
     * @var array<string, string> $context
     */
    private array $context;

    public function __construct(private LoggerInterface $logger, LoggerSettings $settings)
    {
        $this->context = [
            "service.name" => $settings->serviceName,
            "service.version" => $settings->serviceVersion,
            "environment" => $settings->environment,
        ];
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->logger->critical($message, array_merge($this->context, $context));
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->logger->debug($message, array_merge($this->context, $context));
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->logger->error($message, array_merge($this->context, $context));
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->logger->info($message, array_merge($this->context, $context));
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->logger->warning($message, array_merge($this->context, $context));
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->logger->emergency($message, array_merge($this->context, $context));
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->logger->alert($message, array_merge($this->context, $context));
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->logger->notice($message, array_merge($this->context, $context));
    }

    public function log(mixed $level, string|\Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $message, array_merge($this->context, $context));
    }
}
