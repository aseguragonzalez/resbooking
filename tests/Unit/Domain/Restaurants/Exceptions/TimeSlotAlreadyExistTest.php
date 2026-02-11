<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Exceptions;

use Domain\Restaurants\Exceptions\TimeSlotAlreadyExist;
use PHPUnit\Framework\TestCase;

final class TimeSlotAlreadyExistTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new TimeSlotAlreadyExist();

        $this->assertSame('Time slot already exists in restaurant', $exception->getMessage());
    }

    public function testExceptionExtendsDomainException(): void
    {
        $exception = new TimeSlotAlreadyExist();

        $this->assertInstanceOf(\Seedwork\Domain\Exceptions\DomainException::class, $exception);
    }
}
