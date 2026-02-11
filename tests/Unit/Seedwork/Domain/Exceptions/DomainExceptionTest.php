<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain\Exceptions;

use Domain\Restaurants\Exceptions\RestaurantDoesNotExist;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\Exceptions\DomainException;

final class DomainExceptionTest extends TestCase
{
    public function testExceptionPreservesMessage(): void
    {
        $exception = new RestaurantDoesNotExist();

        $this->assertInstanceOf(DomainException::class, $exception);
        $this->assertSame('Restaurant does not exists', $exception->getMessage());
    }

    public function testExceptionPreservesCode(): void
    {
        $exception = new RestaurantDoesNotExist();

        $this->assertSame(0, $exception->getCode());
    }
}
