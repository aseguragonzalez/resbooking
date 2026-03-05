<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Commands;

use Framework\Mvc\Commands\ConsoleOutput;
use Framework\Mvc\Commands\MvcCli;
use PHPUnit\Framework\TestCase;

final class MvcCliTest extends TestCase
{
    /** @var resource */
    private mixed $stdout;

    /** @var resource */
    private mixed $stderr;

    protected function setUp(): void
    {
        $stdout = fopen('php://memory', 'r+');
        $stderr = fopen('php://memory', 'r+');
        \assert(\is_resource($stdout));
        \assert(\is_resource($stderr));
        $this->stdout = $stdout;
        $this->stderr = $stderr;
    }

    protected function tearDown(): void
    {
        if (is_resource($this->stdout)) {
            fclose($this->stdout);
        }
        if (is_resource($this->stderr)) {
            fclose($this->stderr);
        }
    }

    private function createCli(): MvcCli
    {
        $output = new ConsoleOutput($this->stdout, $this->stderr);
        return new MvcCli($output);
    }

    /**
     * @param resource $stream
     */
    private function readStream(mixed $stream): string
    {
        rewind($stream);
        $content = stream_get_contents($stream);
        return $content !== false ? $content : '';
    }

    public function testRunWithNoArgsShowsHelpAndReturnsZero(): void
    {
        $cli = $this->createCli();

        $exitCode = $cli->run(['mvc']);

        $output = $this->readStream($this->stdout);
        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Available commands:', $output);
        $this->assertStringContainsString('create-app', $output);
        $this->assertStringContainsString('initialize-migrations', $output);
        $this->assertStringContainsString('initialize-background-tasks', $output);
    }

    public function testRunWithHelpFlagShowsHelp(): void
    {
        $cli = $this->createCli();

        $exitCode = $cli->run(['mvc', '--help']);

        $output = $this->readStream($this->stdout);
        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Available commands:', $output);
    }

    public function testRunWithUnknownCommandReturnsOne(): void
    {
        $cli = $this->createCli();

        $exitCode = $cli->run(['mvc', 'unknown-command']);

        $this->assertSame(1, $exitCode);
    }

    public function testRunWithCreateAppHelpReturnsZero(): void
    {
        $cli = $this->createCli();

        $exitCode = $cli->run(['mvc', 'create-app', '--help']);

        $output = $this->readStream($this->stdout);
        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('--name=', $output);
        $this->assertStringContainsString('--namespace=', $output);
    }

    public function testRunWithInitializeMigrationsHelpReturnsZero(): void
    {
        $cli = $this->createCli();

        $exitCode = $cli->run(['mvc', 'initialize-migrations', '--help']);

        $output = $this->readStream($this->stdout);
        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('--path=', $output);
    }

    public function testRunWithInitializeBackgroundTasksHelpReturnsZero(): void
    {
        $cli = $this->createCli();

        $exitCode = $cli->run(['mvc', 'initialize-background-tasks', '--help']);

        $output = $this->readStream($this->stdout);
        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('--name=', $output);
        $this->assertStringContainsString('--namespace=', $output);
    }
}
