<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Web\Routes;

use Framework\Web\Routes\InvalidController;
use PHPUnit\Framework\TestCase;

final class InvalidControllerTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new InvalidController('NonExistentController');

        $this->assertSame(
            'Controller NonExistentController is not a valid controller',
            $exception->getMessage()
        );
    }
}
