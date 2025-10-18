<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Logging;

use PHPUnit\Framework\TestCase;
use Monolog\Logger as MonoLogger;
use PHPUnit\Framework\MockObject\MockObject;
use Seedwork\Infrastructure\Logging\MonoLoggerAdapter;

class MonoLoggerAdapterTest extends TestCase
{
    private MonoLogger&MockObject $monoLogger;
    private MonoLoggerAdapter $adapter;

    protected function setUp(): void
    {
        $this->monoLogger = $this->createMock(MonoLogger::class);
        $this->adapter = new MonoLoggerAdapter($this->monoLogger, ['foo' => 'bar']);
    }

    public function testCriticalLogsWithException(): void
    {
        $exception = new \Exception('fail');
        $this->monoLogger->expects($this->once())
            ->method('critical')
            ->with('critical message', ['foo' => 'bar', 'exception' => $exception]);
        $this->adapter->critical('critical message', $exception);
    }

    public function testDebugLogsWithContext(): void
    {
        $this->monoLogger->expects($this->once())
            ->method('debug')
            ->with('debug message', ['foo' => 'bar']);
        $this->adapter->debug('debug message');
    }

    public function testErrorLogsWithException(): void
    {
        $exception = new \Exception('error');
        $this->monoLogger->expects($this->once())
            ->method('error')
            ->with('error message', ['foo' => 'bar', 'exception' => $exception]);
        $this->adapter->error('error message', $exception);
    }

    public function testInfoLogsWithContext(): void
    {
        $this->monoLogger->expects($this->once())
            ->method('info')
            ->with('info message', ['foo' => 'bar']);
        $this->adapter->info('info message');
    }

    public function testWarningLogsWithException(): void
    {
        $exception = new \Exception('warn');
        $this->monoLogger->expects($this->once())
            ->method('warning')
            ->with('warning message', ['foo' => 'bar', 'exception' => $exception]);
        $this->adapter->warning('warning message', $exception);
    }
}
