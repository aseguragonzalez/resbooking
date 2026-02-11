<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Exceptions;

use Domain\Restaurants\Exceptions\DiningAreaDoesNotExist;
use PHPUnit\Framework\TestCase;

final class DiningAreaDoesNotExistTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new DiningAreaDoesNotExist();

        $this->assertSame('Dining area does not exists in restaurant', $exception->getMessage());
    }

    public function testExceptionExtendsDomainException(): void
    {
        $exception = new DiningAreaDoesNotExist();

        $this->assertInstanceOf(\Seedwork\Domain\Exceptions\DomainException::class, $exception);
    }
}
