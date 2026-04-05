<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Routes;

use Framework\Routes\InvalidAction;
use PHPUnit\Framework\TestCase;

final class InvalidActionTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new InvalidAction('MyController', 'invalidAction');

        $this->assertSame(
            "Action 'invalidAction' is not a valid action for controller MyController",
            $exception->getMessage()
        );
    }
}
