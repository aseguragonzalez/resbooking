<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Exceptions;

use Domain\Restaurants\Exceptions\DiningAreaAlreadyExist;
use PHPUnit\Framework\TestCase;

final class DiningAreaAlreadyExistTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new DiningAreaAlreadyExist();

        $this->assertSame('Dining area already exists in restaurant', $exception->getMessage());
    }

    public function testExceptionExtendsDomainException(): void
    {
        $exception = new DiningAreaAlreadyExist();

        $this->assertInstanceOf(\Seedwork\Domain\Exceptions\DomainException::class, $exception);
    }
}
