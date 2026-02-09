<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks;

use DI\Container;
use Framework\Application;
use Framework\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasks;
use Framework\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasksCommand;
use Framework\BackgroundTasks\Domain\TaskHandler;
use Framework\BackgroundTasks\Domain\TaskHandlerRegistry;
use Framework\BackgroundTasks\Infrastructure\MapTaskHandlerRegistry;
use Framework\Logging\LoggerAdapter;
use Framework\Logging\LoggerSettings;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use PDO;
use Psr\Log\LoggerInterface;

abstract class BackgroundTasksApp extends Application
{
    public function __construct(Container $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }

    /**
     * @param int|null $argc
     * @param array<string> $argv
     */
    public function run(?int $argc = null, array $argv = []): int
    {
        $this->configureSettings();
        $this->configureLogging();
        $this->configureDependencies();

        try {
            /** @var ProcessPendingTasks $processPendingTasks */
            $processPendingTasks = $this->container->get(ProcessPendingTasks::class);
            $processPendingTasks->execute(new ProcessPendingTasksCommand(limit: 100));
            return 0;
        } catch (\Throwable $e) {
            /** @var LoggerInterface $logger */
            $logger = $this->container->get(LoggerInterface::class);
            $logger->error('Error running background tasks: {error}', ['error' => $e->getMessage()]);
            return 1;
        }
    }

    protected function configureDependencies(): void
    {
        /** @var BackgroundTasksSettings $settings */
        $settings = $this->container->get(BackgroundTasksSettings::class);
        $connection = new PDO(
            $settings->getDsn(),
            $settings->user,
            $settings->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        $this->container->set(PDO::class, $connection);

        $registry = new MapTaskHandlerRegistry();
        foreach ($settings->handlerMap as $taskType => $handlerClass) {
            $handler = $this->container->get($handlerClass);

            if (! $handler instanceof TaskHandler) {
                $resolvedType = \is_object($handler) ? \get_class($handler) : \gettype($handler);

                throw new \RuntimeException(\sprintf(
                    'Handler for task type "%s" must implement %s, got %s',
                    (string) $taskType,
                    TaskHandler::class,
                    $resolvedType
                ));
            }
            $registry->register($taskType, $handler);
        }
        $this->container->set(TaskHandlerRegistry::class, $registry);

        Dependencies::configure($this->container);
    }

    protected function configureLogging(): void
    {
        /** @var LoggerSettings $loggerSettings */
        $loggerSettings = $this->container->get(LoggerSettings::class);

        $handler = new StreamHandler(
            stream: 'php://stdout',
            level: $this->getLogLevelFromSettings($loggerSettings)
        );
        $handler->setFormatter(new LineFormatter(
            format: '[%datetime%] %level_name%: %message%',
            dateFormat: 'Y-m-d H:i:s',
            allowInlineLineBreaks: true,
            ignoreEmptyContextAndExtra: true,
            includeStacktraces: false,
        ));

        $logger = new Logger($loggerSettings->serviceName);
        $logger->pushHandler($handler);
        $logger->pushProcessor(new PsrLogMessageProcessor());

        $loggerAdapter = new LoggerAdapter(logger: $logger);
        $this->container->set(LoggerInterface::class, $loggerAdapter);
    }

    protected function configureSettings(): void
    {
        $this->container->set(
            LoggerSettings::class,
            new LoggerSettings(
                environment: getenv('ENVIRONMENT') ?: 'local',
                serviceName: getenv('BACKGROUND_TASKS_SERVICE_NAME') ?: 'background-tasks',
                serviceVersion: getenv('BACKGROUND_TASKS_SERVICE_VERSION') ?: '1.0.0',
                logLevel: getenv('BACKGROUND_TASKS_LOG_LEVEL') ?: 'debug',
            ),
        );
        $this->container->set(
            BackgroundTasksSettings::class,
            new BackgroundTasksSettings(
                host: getenv('BACKGROUND_TASKS_DATABASE_HOST') ?: 'localhost',
                database: getenv('BACKGROUND_TASKS_DATABASE_NAME') ?: 'reservations',
                user: getenv('BACKGROUND_TASKS_DATABASE_USER') ?: 'root',
                password: getenv('BACKGROUND_TASKS_DATABASE_PASSWORD') ?: '',
                handlerMap: $this->getHandlerMap(),
            ),
        );
    }

    /**
     * Register task type => handler class. Override in concrete app to register handlers.
     *
     * @return array<string, string>
     */
    abstract protected function getHandlerMap(): array;

    private function getLogLevelFromSettings(LoggerSettings $loggerSettings): Level
    {
        $logLevel = $loggerSettings->logLevel;
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        if (!in_array($logLevel, $logLevels)) {
            throw new \InvalidArgumentException("Invalid log level: {$logLevel}");
        }
        return Level::fromName($logLevel);
    }
}
