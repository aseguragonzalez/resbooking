<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Exceptions;

use Domain\Restaurants\Exceptions\RestaurantDoesNotExist;
use PHPUnit\Framework\TestCase;

final class RestaurantDoesNotExistTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new RestaurantDoesNotExist();

        $this->assertSame('Restaurant does not exists', $exception->getMessage());
    }

    public function testExceptionExtendsDomainException(): void
    {
        $exception = new RestaurantDoesNotExist();

        $this->assertInstanceOf(\Seedwork\Domain\Exceptions\DomainException::class, $exception);
    }
}
