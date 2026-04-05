<?php

declare(strict_types=1);

namespace Framework\Commands;

final class ConsoleOutput
{
    private bool $colorsEnabled;

    /** @var resource */
    private mixed $stdout;

    /** @var resource */
    private mixed $stderr;

    /**
     * @param resource|null $stdout
     * @param resource|null $stderr
     */
    public function __construct(mixed $stdout = null, mixed $stderr = null, bool $colorsEnabled = true)
    {
        $this->colorsEnabled = $colorsEnabled;
        $this->stdout = $stdout ?? \STDOUT;
        $this->stderr = $stderr ?? \STDERR;
    }

    public function info(string $message): void
    {
        $this->writeln($this->colorize($message, '36'));
    }

    public function success(string $message): void
    {
        $this->writeln($this->colorize($message, '32'));
    }

    public function error(string $message): void
    {
        fwrite($this->stderr, $this->colorize($message, '31') . PHP_EOL);
    }

    public function line(string $message = ''): void
    {
        $this->writeln($message);
    }

    private function writeln(string $message): void
    {
        fwrite($this->stdout, $message . PHP_EOL);
    }

    private function colorize(string $message, string $colorCode): string
    {
        if (!$this->colorsEnabled) {
            return $message;
        }

        return "\033[{$colorCode}m{$message}\033[0m";
    }
}
