<?php

declare(strict_types=1);

namespace Framework\Logging;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonoLogger;

final readonly class MonoLoggerBuilder
{
    public function __construct(private LoggerSettings $loggerSettings)
    {
    }

    public function build(): MonoLogger
    {
        $handler = new StreamHandler($this->loggerSettings->stream, $this->getLogLevel());
        $handler->setFormatter(new JsonFormatter());
        $logger = new MonoLogger($this->loggerSettings->serviceName);
        $logger->pushHandler($handler);
        return $logger;
    }

    private function getLogLevel(): Level
    {
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $logLevel = strtolower($this->loggerSettings->logLevel);
        if (!in_array($logLevel, $logLevels)) {
            throw new \InvalidArgumentException("Invalid log level: {$this->loggerSettings->logLevel}");
        }

        return Level::fromName($logLevel);
    }
}
