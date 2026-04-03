<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks;

use DI\Container;
use Framework\Application;
use Framework\Mvc\Config\MvcConfig;
use Framework\Mvc\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasks;
use Framework\Mvc\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasksCommand;
use Framework\Mvc\BackgroundTasks\Domain\TaskHandler;
use Framework\Mvc\BackgroundTasks\Domain\TaskHandlerRegistry;
use Framework\Mvc\BackgroundTasks\Infrastructure\MapTaskHandlerRegistry;
use Framework\Logging\LoggerAdapter;
use Framework\Logging\LoggerSettings;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use PDO;
use Psr\Log\LoggerInterface;

abstract class BaseBackgroundTasksApp extends Application
{
    private bool $shutdownRequested = false;

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
        $this->configure();
        $this->configureDependencies();

        $intervalSeconds = $this->resolvePollIntervalSeconds($argv);
        $this->installSignalHandlersIfLooping($intervalSeconds > 0);

        while (true) {
            $code = $this->runBatch();
            if ($code !== 0) {
                return $code;
            }
            if ($intervalSeconds <= 0) {
                return 0;
            }
            if ($this->shutdownRequested) {
                return 0;
            }
            sleep($intervalSeconds);
            // @phpstan-ignore if.alwaysFalse (pcntl handlers may set shutdownRequested during sleep)
            if ($this->shutdownRequested) {
                return 0;
            }
        }
    }

    /**
     * @param array<string> $argv
     */
    private function resolvePollIntervalSeconds(array $argv): int
    {
        $fromCli = $this->parseIntervalFromArgv($argv);
        if ($fromCli > 0) {
            return $fromCli;
        }

        $appRoot = dirname($this->basePath);
        $config = MvcConfig::load($appRoot);

        return $config->effectiveBackgroundTasksPollIntervalSeconds();
    }

    /**
     * @param array<string> $argv
     */
    private function parseIntervalFromArgv(array $argv): int
    {
        foreach ($argv as $i => $arg) {
            if (str_starts_with($arg, '--interval=')) {
                $n = (int) substr($arg, 11);

                return $n > 0 ? $n : 0;
            }
            if ($arg === '--interval' && isset($argv[$i + 1])) {
                $n = (int) $argv[(int) $i + 1];

                return $n > 0 ? $n : 0;
            }
        }

        return 0;
    }

    private function installSignalHandlersIfLooping(bool $looping): void
    {
        if (!$looping || !function_exists('pcntl_async_signals')) {
            return;
        }

        pcntl_async_signals(true);
        pcntl_signal(SIGINT, function (): void {
            $this->shutdownRequested = true;
        });
        pcntl_signal(SIGTERM, function (): void {
            $this->shutdownRequested = true;
        });
    }

    private function runBatch(): int
    {
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

        // configure background tasks dependencies
        Dependencies::configure($this->container);

        /** @var TaskHandlerRegistry $registry */
        $registry = $this->container->get(TaskHandlerRegistry::class);
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

    abstract protected function configure(): void;

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
