<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Exceptions;

use Domain\Restaurants\Exceptions\DiningAreaNotFound;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;

final class DiningAreaNotFoundTest extends TestCase
{
    public function testExceptionHasExpectedMessageWithDiningAreaId(): void
    {
        $diningAreaId = EntityId::fromString('area-123');

        $exception = new DiningAreaNotFound($diningAreaId);

        $this->assertSame('Dining area not found: area-123', $exception->getMessage());
    }

    public function testExceptionExtendsDomainException(): void
    {
        $exception = new DiningAreaNotFound(EntityId::fromString('some-id'));

        $this->assertInstanceOf(\Seedwork\Domain\Exceptions\DomainException::class, $exception);
    }
}
