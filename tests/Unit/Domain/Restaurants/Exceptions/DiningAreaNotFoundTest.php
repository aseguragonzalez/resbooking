<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Exceptions;

use Domain\Restaurants\Exceptions\DiningAreaNotFound;
use Domain\Restaurants\ValueObjects\DiningAreaId;
use PHPUnit\Framework\TestCase;

final class DiningAreaNotFoundTest extends TestCase
{
    public function testExceptionHasExpectedMessageWithDiningAreaId(): void
    {
        $diningAreaId = DiningAreaId::fromString('area-123');

        $exception = new DiningAreaNotFound($diningAreaId);

        $this->assertSame('Dining area not found: area-123', $exception->getMessage());
    }

    public function testExceptionExtendsDomainException(): void
    {
        $exception = new DiningAreaNotFound(DiningAreaId::fromString('some-id'));

        $this->assertInstanceOf(\SeedWork\Domain\Exceptions\DomainException::class, $exception);
    }
}
