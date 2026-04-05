<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Apps\BackgroundTasks;

use Framework\Apps\BackgroundTasks\TaskHandlerClassMap;
use PHPUnit\Framework\TestCase;

final class TaskHandlerClassMapTest extends TestCase
{
    public function testGetHandlerClassReturnsNullWhenMissing(): void
    {
        $map = new TaskHandlerClassMap([]);

        $this->assertNull($map->getHandlerClass('missing'));
    }

    public function testGetHandlerClassReturnsFqcnWhenPresent(): void
    {
        $map = new TaskHandlerClassMap([
            'send_email' => self::class,
        ]);

        $this->assertSame(self::class, $map->getHandlerClass('send_email'));
    }

    public function testConstructorRejectsEmptyTaskType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TaskHandlerClassMap(['' => self::class]);
    }

    public function testConstructorRejectsEmptyClassName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TaskHandlerClassMap(['type' => '']);
    }
}
