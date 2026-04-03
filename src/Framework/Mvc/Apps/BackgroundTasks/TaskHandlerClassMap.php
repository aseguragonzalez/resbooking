<?php

declare(strict_types=1);

namespace Framework\Mvc\BackgroundTasks;

/**
 * Maps task type strings to TaskHandler implementation class names.
 */
final readonly class TaskHandlerClassMap
{
    /**
     * @param array<string, string> $map task type => handler class FQCN
     */
    public function __construct(private array $map)
    {
        foreach ($this->map as $taskType => $className) {
            if ($taskType === '' || $className === '') {
                throw new \InvalidArgumentException('Task type and handler class name must be non-empty strings');
            }
        }
    }

    public function getHandlerClass(string $taskType): ?string
    {
        return $this->map[$taskType] ?? null;
    }
}
