<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Domain;

final readonly class Task
{
    /**
     * Key-value dictionary; values are JSON-serializable (scalars, null, or nested associative arrays).
     *
     * @param array<string, mixed> $arguments
     */
    private function __construct(
        public string $taskType,
        public array $arguments = [],
    ) {
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public static function new(string $taskType, array $arguments = []): self
    {
        if ($taskType === '') {
            throw new \InvalidArgumentException('Task type must not be empty');
        }
        self::validateArguments($arguments);

        return new self(taskType: $taskType, arguments: $arguments);
    }

    /**
     * Recursively validate: string keys (non-empty), values are scalars, null, or arrays.
     *
     * @param array<mixed, mixed> $arguments
     */
    private static function validateArguments(array $arguments): void
    {
        foreach ($arguments as $key => $value) {
            if (!is_string($key) || $key === '') {
                throw new \InvalidArgumentException('Argument keys must be non-empty strings');
            }
            if (is_array($value)) {
                self::validateArguments($value);
                continue;
            }
            if ($value !== null && !is_scalar($value)) {
                throw new \InvalidArgumentException(
                    'Argument values must be JSON-serializable (scalars, null, or nested associative arrays)'
                );
            }
        }
    }
}
