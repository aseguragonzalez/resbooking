<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Exceptions;

use Domain\Restaurants\Exceptions\UserAlreadyExist;
use PHPUnit\Framework\TestCase;

final class UserAlreadyExistTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new UserAlreadyExist();

        $this->assertSame('User already exists in restaurant', $exception->getMessage());
    }

    public function testExceptionExtendsDomainException(): void
    {
        $exception = new UserAlreadyExist();

        $this->assertInstanceOf(\Seedwork\Domain\Exceptions\DomainException::class, $exception);
    }
}
