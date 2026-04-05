<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Apps\BackgroundTasks\Infrastructure;

use DI\Container;
use Framework\BackgroundTasks\Infrastructure\ContainerTaskHandlerRegistry;
use Framework\BackgroundTasks\TaskHandlerClassMap;
use PHPUnit\Framework\TestCase;

final class ContainerTaskHandlerRegistryTest extends TestCase
{
    public function testGetHandlerReturnsNullWhenTypeNotInMap(): void
    {
        $container = new Container();
        $container->set(TaskHandlerClassMap::class, new TaskHandlerClassMap([]));

        $registry = $container->get(ContainerTaskHandlerRegistry::class);
        \assert($registry instanceof ContainerTaskHandlerRegistry);

        $this->assertNull($registry->getHandler('unknown_type'));
    }

    public function testGetHandlerReturnsResolvedHandler(): void
    {
        $handler = new StubTaskHandler();
        $container = new Container();
        $container->set(TaskHandlerClassMap::class, new TaskHandlerClassMap([
            'send_email' => StubTaskHandler::class,
        ]));
        $container->set(StubTaskHandler::class, $handler);

        $registry = $container->get(ContainerTaskHandlerRegistry::class);
        \assert($registry instanceof ContainerTaskHandlerRegistry);

        $this->assertSame($handler, $registry->getHandler('send_email'));
    }

    public function testGetHandlerReturnsSameInstanceOnSecondCall(): void
    {
        $handler = new StubTaskHandler();
        $container = new Container();
        $container->set(TaskHandlerClassMap::class, new TaskHandlerClassMap([
            'send_email' => StubTaskHandler::class,
        ]));
        $container->set(StubTaskHandler::class, $handler);

        $registry = $container->get(ContainerTaskHandlerRegistry::class);
        \assert($registry instanceof ContainerTaskHandlerRegistry);

        $this->assertSame($registry->getHandler('send_email'), $registry->getHandler('send_email'));
    }

    public function testGetHandlerThrowsWhenResolvedObjectIsNotTaskHandler(): void
    {
        $container = new Container();
        $container->set(TaskHandlerClassMap::class, new TaskHandlerClassMap([
            'bad' => NotATaskHandler::class,
        ]));
        $container->set(NotATaskHandler::class, new NotATaskHandler());

        $registry = $container->get(ContainerTaskHandlerRegistry::class);
        \assert($registry instanceof ContainerTaskHandlerRegistry);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Handler for task type "bad" must implement');
        $registry->getHandler('bad');
    }
}
