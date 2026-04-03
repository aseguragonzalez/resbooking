<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Commands;

use Framework\Mvc\Commands\ConsoleOutput;
use Framework\Mvc\Commands\MigrationsTestCommand;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class MigrationsTestCommandTest extends TestCase
{
    /** @var resource */
    private mixed $stdout;

    /** @var resource */
    private mixed $stderr;

    private ConsoleOutput $consoleOutput;

    protected function setUp(): void
    {
        vfsStream::setup('project', null, [
            'module' => [
                'index.php' => '<?php',
                'migrations' => [],
            ],
        ]);

        $stdout = fopen('php://memory', 'r+');
        $stderr = fopen('php://memory', 'r+');
        \assert(\is_resource($stdout));
        \assert(\is_resource($stderr));
        $this->stdout = $stdout;
        $this->stderr = $stderr;
        $this->consoleOutput = new ConsoleOutput($this->stdout, $this->stderr);
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

    public function testExecuteDelegatesToMigrationRunnerWithTestArg(): void
    {
        $capturedIndexPath = null;
        $capturedForwardArgs = null;
        $runner = function (
            string $indexPath,
            array $forwardArgs,
        ) use (
            &$capturedIndexPath,
            &$capturedForwardArgs,
        ): int {
            $capturedIndexPath = $indexPath;
            $capturedForwardArgs = $forwardArgs;
            return 0;
        };
        $command = new MigrationsTestCommand($this->consoleOutput, $runner);
        $migrationsPath = vfsStream::url('project/module/migrations');
        $expectedIndex = vfsStream::url('project/module/index.php');

        $exitCode = $command->execute([
            '--path=' . $migrationsPath,
            '--migration=20260123183421',
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertSame($expectedIndex, $capturedIndexPath);
        $this->assertSame(
            ['--migrations-base=' . $migrationsPath, '--test=20260123183421'],
            $capturedForwardArgs,
        );
    }

    public function testExecuteReturnsRunnerExitCode(): void
    {
        $runner = fn (string $indexPath, array $forwardArgs): int => 1;
        $command = new MigrationsTestCommand($this->consoleOutput, $runner);
        $migrationsPath = vfsStream::url('project/module/migrations');

        $exitCode = $command->execute([
            '--path=' . $migrationsPath,
            '--migration=20260123183421',
        ]);

        $this->assertSame(1, $exitCode);
    }

    public function testExecuteFailsWhenPathNotProvided(): void
    {
        $runner = fn (string $indexPath, array $forwardArgs): int => 0;
        $command = new MigrationsTestCommand($this->consoleOutput, $runner);

        $exitCode = $command->execute(['--migration=20260123183421']);

        $this->assertSame(1, $exitCode);
    }

    public function testExecuteFailsWhenMigrationNotProvided(): void
    {
        $runner = fn (string $indexPath, array $forwardArgs): int => 0;
        $command = new MigrationsTestCommand($this->consoleOutput, $runner);
        $migrationsPath = vfsStream::url('project/module/migrations');

        $exitCode = $command->execute(['--path=' . $migrationsPath]);

        $this->assertSame(1, $exitCode);
    }

    public function testExecuteFailsWhenDirectoryDoesNotExist(): void
    {
        $runner = fn (string $indexPath, array $forwardArgs): int => 0;
        $command = new MigrationsTestCommand($this->consoleOutput, $runner);

        $exitCode = $command->execute([
            '--path=' . vfsStream::url('project/nonexistent'),
            '--migration=20260123183421',
        ]);

        $this->assertSame(1, $exitCode);
    }

    public function testHelpFlagReturnsZero(): void
    {
        $runner = fn (string $indexPath, array $forwardArgs): int => 0;
        $command = new MigrationsTestCommand($this->consoleOutput, $runner);

        $exitCode = $command->execute(['--help']);

        $this->assertSame(0, $exitCode);
    }

    public function testShortHelpFlagReturnsZero(): void
    {
        $runner = fn (string $indexPath, array $forwardArgs): int => 0;
        $command = new MigrationsTestCommand($this->consoleOutput, $runner);

        $exitCode = $command->execute(['-h']);

        $this->assertSame(0, $exitCode);
    }

    public function testHelpOutputContainsUsageInfo(): void
    {
        $runner = fn (string $indexPath, array $forwardArgs): int => 0;
        $command = new MigrationsTestCommand($this->consoleOutput, $runner);

        $command->execute(['--help']);

        rewind($this->stdout);
        $output = stream_get_contents($this->stdout);
        \assert(\is_string($output));
        $this->assertStringContainsString('--path=', $output);
        $this->assertStringContainsString('--migration=', $output);
        $this->assertStringContainsString('--force', $output);
    }

    public function testOutputContainsInfoMessage(): void
    {
        $runner = fn (string $indexPath, array $forwardArgs): int => 0;
        $command = new MigrationsTestCommand($this->consoleOutput, $runner);
        $migrationsPath = vfsStream::url('project/module/migrations');

        $command->execute([
            '--path=' . $migrationsPath,
            '--migration=20260123183421',
        ]);

        rewind($this->stdout);
        $output = stream_get_contents($this->stdout);
        \assert(\is_string($output));
        $this->assertStringContainsString('Testing migration', $output);
        $this->assertStringContainsString('20260123183421', $output);
        $this->assertStringContainsString('Entrypoint:', $output);
    }
}
