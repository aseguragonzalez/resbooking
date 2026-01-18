<?php

declare(strict_types=1);

namespace Framework\Migrations;

use DI\Container;
use Framework\Application;
use Framework\Logging\LoggerAdapter;
use Framework\Logging\LoggerSettings;
use Framework\Migrations\Application\RunMigrations;
use Framework\Migrations\Application\TestMigration;
use Framework\Migrations\Application\TestMigrationCommand;
use Framework\Migrations\MigrationSettings;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

final class MigrationApp extends Application
{
    public function __construct(Container $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }

    /**
     * Run the application with the given arguments.
     * @param int|null $argc The number of arguments passed to the application. Default is null.
     * @param array<string> $argv The arguments to pass to the application. Default is an empty array.
     * @return int The exit code of the application.
     */
    public function run(?int $argc = null, array $argv = []): int
    {
        $this->configureSettings();
        $this->configureLogging();
        $this->configureDependencies();

        try {
            $arguments = $this->parseArguments($argv);

            if ($arguments['command'] === 'test') {
                /** @var string $migrationName */
                $migrationName = $arguments['args'];
                /** @var TestMigration $testMigration */
                $testMigration = $this->container->get(TestMigration::class);
                $testMigration->execute(new TestMigrationCommand(
                    migrationName: $migrationName,
                    basePath: $this->basePath,
                ));
            } elseif ($arguments['command'] === 'run') {
                /** @var RunMigrations $runMigrations */
                $runMigrations = $this->container->get(RunMigrations::class);
                $runMigrations->execute(basePath: $this->basePath);
            }

            return 0;
        } catch (\Exception $e) {
            /** @var LoggerInterface $logger */
            $logger = $this->container->get(LoggerInterface::class);
            $logger->error('Error running migrations: {error}', ['error' => $e->getMessage()]);
            return 1;
        }
    }

    protected function configureDependencies(): void
    {
        Dependencies::configure($this->container);
    }

    protected function configureLogging(): void
    {
        /** @var LoggerSettings $loggerSettings */
        $loggerSettings = $this->container->get(LoggerSettings::class);

        $handler = new StreamHandler(
            stream: $loggerSettings->stream,
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

    private function getLogLevelFromSettings(LoggerSettings $loggerSettings): Level
    {
        $logLevel = $loggerSettings->logLevel;
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        if (!in_array($logLevel, $logLevels)) {
            throw new \InvalidArgumentException("Invalid log level: {$logLevel}");
        }
        return Level::fromName($logLevel);
    }

    protected function configureSettings(): void
    {
        $this->container->set(
            LoggerSettings::class,
            new LoggerSettings(
                environment: getenv('ENVIRONMENT') ?: 'local',
                serviceName: getenv('MIGRATIONS_SERVICE_NAME') ?: 'migrations',
                serviceVersion: getenv('MIGRATIONS_SERVICE_VERSION') ?: '1.0.0',
                logLevel: getenv('MIGRATIONS_LOG_LEVEL') ?: 'debug',
                stream: getenv('MIGRATIONS_LOG_STREAM') ?: 'php://stdout',
            ),
        );
        $this->container->set(
            MigrationSettings::class,
            new MigrationSettings(
                host: getenv('MIGRATIONS_DATABASE_HOST') ?: 'mariadb',
                database: getenv('MIGRATIONS_DATABASE_NAME') ?: 'migrations',
                user: getenv('MIGRATIONS_DATABASE_USER') ?: 'migrations',
                password: getenv('MIGRATIONS_DATABASE_PASSWORD') ?: '',
            ),
        );
    }

    /**
     * Parse the arguments and return the command and the migration name.
     * @param array<string> $argv The arguments to pass to the application. Default is an empty array.
     * @return array<string, string> The command and the arguments.
     */
    private function parseArguments(array $argv = []): array
    {
        if (empty($argv)) {
            return [
                'command' => 'run',
                'args' => '',
            ];
        }

        if (count($argv) === 1) {
            $migrationName = str_replace('--test=', '', $argv[0] ?? '');
            return [
                'command' => 'test',
                'args' => $migrationName,
            ];
        }

        throw new \InvalidArgumentException('Invalid command');
    }
}
