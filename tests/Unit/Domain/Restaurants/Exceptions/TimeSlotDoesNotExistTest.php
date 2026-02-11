<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Exceptions;

use Domain\Restaurants\Exceptions\TimeSlotDoesNotExist;
use PHPUnit\Framework\TestCase;

final class TimeSlotDoesNotExistTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new TimeSlotDoesNotExist();

        $this->assertSame('Time slot does not exists in restaurant', $exception->getMessage());
    }

    public function testExceptionExtendsDomainException(): void
    {
        $exception = new TimeSlotDoesNotExist();

        $this->assertInstanceOf(\Seedwork\Domain\Exceptions\DomainException::class, $exception);
    }
}
