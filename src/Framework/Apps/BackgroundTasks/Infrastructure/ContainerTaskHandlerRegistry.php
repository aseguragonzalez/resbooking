<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Infrastructure;

use DI\Container;
use Framework\BackgroundTasks\Domain\TaskHandler;
use Framework\BackgroundTasks\Domain\TaskHandlerRegistry;
use Framework\BackgroundTasks\TaskHandlerClassMap;

final class ContainerTaskHandlerRegistry implements TaskHandlerRegistry
{
    /**
     * @var array<string, TaskHandler>
     */
    private array $resolved = [];

    public function __construct(
        private readonly TaskHandlerClassMap $handlerClassMap,
        private readonly Container $container,
    ) {
    }

    public function getHandler(string $taskType): ?TaskHandler
    {
        if (isset($this->resolved[$taskType])) {
            return $this->resolved[$taskType];
        }

        $handlerClass = $this->handlerClassMap->getHandlerClass($taskType);
        if ($handlerClass === null) {
            return null;
        }

        $handler = $this->container->get($handlerClass);
        if (!$handler instanceof TaskHandler) {
            $resolvedType = \is_object($handler) ? \get_class($handler) : \gettype($handler);

            throw new \RuntimeException(\sprintf(
                'Handler for task type "%s" must implement %s, got %s',
                $taskType,
                TaskHandler::class,
                $resolvedType
            ));
        }

        $this->resolved[$taskType] = $handler;

        return $handler;
    }
}
