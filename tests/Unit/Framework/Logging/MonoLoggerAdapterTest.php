<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Logging;

use Monolog\Logger as MonoLogger;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Framework\Logging\LoggerSettings;
use Framework\Logging\MonoLoggerAdapter;
use Framework\Logging\MonoLoggerBuilder;

class MonoLoggerAdapterTest extends TestCase
{
    private MonoLogger&MockObject $monoLogger;
    private MonoLoggerAdapter $adapter;
    private LoggerSettings $settings;

    protected function setUp(): void
    {
        $this->monoLogger = $this->createMock(MonoLogger::class);
        $this->settings = new LoggerSettings(
            environment: 'local',
            serviceName: 'test',
            serviceVersion: '1.0.0',
        );
        $monoLoggerBuilder = $this->createMock(MonoLoggerBuilder::class);
        $monoLoggerBuilder->method('build')->willReturn($this->monoLogger);
        $this->adapter = new MonoLoggerAdapter($monoLoggerBuilder, $this->settings);
    }

    public function testCriticalLogsWithException(): void
    {
        $exception = new \Exception('fail');
        $this->monoLogger->expects($this->once())
            ->method('critical')
            ->with('critical message', [
                'service.name' => 'test',
                'service.version' => '1.0.0',
                'environment' => 'local',
                'exception' => $exception,
            ]);
        $this->adapter->critical('critical message', $exception);
    }

    public function testDebugLogsWithContext(): void
    {
        $this->monoLogger->expects($this->once())
            ->method('debug')
            ->with('debug message', [
                'service.name' => 'test',
                'service.version' => '1.0.0',
                'environment' => 'local',
            ]);
        $this->adapter->debug('debug message');
    }

    public function testErrorLogsWithException(): void
    {
        $exception = new \Exception('error');
        $this->monoLogger->expects($this->once())
            ->method('error')
            ->with('error message', [
                'service.name' => 'test',
                'service.version' => '1.0.0',
                'environment' => 'local',
                'exception' => $exception
            ]);
        $this->adapter->error('error message', $exception);
    }

    public function testInfoLogsWithContext(): void
    {
        $this->monoLogger->expects($this->once())
            ->method('info')
            ->with('info message', [
                'service.name' => 'test',
                'service.version' => '1.0.0',
                'environment' => 'local',
            ]);
        $this->adapter->info('info message');
    }

    public function testWarningLogsWithException(): void
    {
        $exception = new \Exception('warn');
        $this->monoLogger->expects($this->once())
            ->method('warning')
            ->with('warning message', [
                'service.name' => 'test',
                'service.version' => '1.0.0',
                'environment' => 'local',
                'exception' => $exception
            ]);
        $this->adapter->warning('warning message', $exception);
    }
}
