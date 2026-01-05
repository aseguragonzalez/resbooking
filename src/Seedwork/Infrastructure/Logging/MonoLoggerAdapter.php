<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Logging;

use Monolog\Logger as MonoLogger;
use Seedwork\Application\Logging\Logger;

final readonly class MonoLoggerAdapter implements Logger
{
    /**
     * @var array<string, string> $context
     */
    private array $context;
    private MonoLogger $logger;

    public function __construct(MonoLoggerBuilder $loggerBuilder, LoggerSettings $settings)
    {
        $this->context = [
            "service.name" => $settings->serviceName,
            "service.version" => $settings->serviceVersion,
            "environment" => $settings->environment,
        ];
        $this->logger = $loggerBuilder->build();
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
