<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks;

use Framework\Application;
use Psr\Container\ContainerInterface;
use Framework\Config\MvcConfig;
use Framework\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasks;
use Framework\BackgroundTasks\Application\ProcessPendingTasks\ProcessPendingTasksCommand;
use Psr\Log\LoggerInterface;

final class BackgroundTasksApp extends Application
{
    private bool $shutdownRequested = false;

    public function __construct(ContainerInterface $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }

    /**
     * @param int|null $argc
     * @param array<string> $argv
     */
    public function run(?int $argc = null, array $argv = []): int
    {
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
            // pcntl_signal handlers may set shutdownRequested during sleep(); PHPStan cannot see that.
            // @phpstan-ignore if.alwaysFalse
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
}
