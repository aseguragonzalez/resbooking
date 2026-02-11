<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Exceptions;

use Domain\Restaurants\Exceptions\UserDoesNotExist;
use PHPUnit\Framework\TestCase;

final class UserDoesNotExistTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new UserDoesNotExist();

        $this->assertSame('User does not exists in restaurant', $exception->getMessage());
    }

    public function testExceptionExtendsDomainException(): void
    {
        $exception = new UserDoesNotExist();

        $this->assertInstanceOf(\Seedwork\Domain\Exceptions\DomainException::class, $exception);
    }
}
